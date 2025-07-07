<?php

namespace App\Services;

use App\Helpers\Common;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductService extends BaseService
{
    protected Model $model;

    public function __construct()
    {
        $this->model = new Product();
    }

    public function getProducts(array $params)
    {
        $keyword = $params['q'] ?? null;
        $categoryId = $params['category_id'] ?? null;
        $sortBy = $params['sort_by'] ?? 'created_at';
        $sortOrder = $params['sort_order'] ?? 'desc';
        $perPage = $params['per_page'] ?? 10;
        $take = $params['take'] ?? null;

        try {
            $products = Product::with('category')
                ->when($keyword, fn($query) => $query->where('name', 'like', "%$keyword%"))
                ->when($categoryId, fn($query) => $query->where('category_id', $categoryId))
                ->orderBy($sortBy, $sortOrder);

            if (!is_null($take)) {
                $result = $products->take($take)->get();
            } else {
                $result = $products->paginate($perPage);
            }

            return $this->transformPaginationResult($result, fn($item) => $item->transform());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function getAllProducts(array $params)
    {
        $keyword = $params['q'] ?? null;
        $categoryId = $params['category_id'] ?? null;
        $sortBy = $params['sort_by'] ?? 'created_at';
        $sortOrder = $params['sort_order'] ?? 'desc';

        try {
            $products = Product::with('category')
                ->when($keyword, fn($query) => $query->where('name', 'like', "%$keyword%"))
                ->when($categoryId, fn($query) => $query->where('category_id', $categoryId))
                ->orderBy($sortBy, $sortOrder)
                ->get();

            return $this->transformPaginationResult($products, fn($item) => $item->transform());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function getAllProductsForExport(array $params)
    {
        $keyword = $params['q'] ?? null;
        $categoryId = $params['category_id'] ?? null;
        $sortBy = $params['sort_by'] ?? 'created_at';
        $sortOrder = $params['sort_order'] ?? 'desc';

        try {
            $products = Product::with('category')
                ->when($keyword, fn($query) => $query->where('name', 'like', "%$keyword%"))
                ->when($categoryId, fn($query) => $query->where('category_id', $categoryId))
                ->orderBy($sortBy, $sortOrder)
                ->get();

            return $products;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function getProductById($id)
    {
        try {
            return Product::with('category')->find($id);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }
    public function createProduct(array $data)
    {
        try {
            if (!empty($data['image'])) {
                $path = Common::storeBase64Image($data['image']);
                if ($path) {
                    $data['image'] = $path;
                } else {
                    throw new \Exception('Invalid base64 image data.');
                }
            }

            return $this->create($data);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }
    public function updateProduct(Product $product, array $data)
    {
        // Kiểm tra nếu tồn tại key 'image'
        if (isset($data['image'])) {
            $image = $data['image'];

            // Nếu là base64 image
            if (preg_match('/^data:image\/(\w+);base64,/', $image)) {
                $data['image'] = Common::storeBase64Image($image);
            } else {
                // Nếu không phải base64 (có thể là đường dẫn cũ) => giữ nguyên
                unset($data['image']);
            }
        }

        $product->update($data);
        return $product;
    }
    public function deleteProduct(Product $product)
    {
        return $product->delete();
    }


    // Tạo mới hoặc cập nhật sản phẩm từ dòng CSV
    public function updateOrCreateProduct($data, $isUpdate = false)
    {
        // Chuẩn hóa dữ liệu
        $fields = [
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'image' => $data['image'],
            'category_id' => $data['category_id'],
            'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : true,
            'stock' => $data['stock'],
            'discount' => $data['discount'] ?? null,
            'size' => $data['size'] ?? [],
        ];

        if ($isUpdate && !empty($data['id'])) {
            $product = Product::find($data['id']);
            if (!$product) {
                throw new \Exception('Product not found: ' . $data['id']);
            }
            $product->update($fields);
        } else {
            $product = Product::create($fields);
        }

        // Sync options & toppings
        if (isset($data['options'])) {
            $product->options()->sync($data['options']);
        }
        if (isset($data['toppings'])) {
            $product->toppings()->sync($data['toppings']);
        }

        return $product->fresh(['options', 'toppings']);
    }

    /**
     * Import products from file (xlsx/csv), lấy sheet1 nếu là xlsx
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    public function importProductsFromFile($file)
    {
        $extension = $file->getClientOriginalExtension();
        if ($extension === 'xlsx') {
            // Đọc sheet1
            $rows = Excel::toArray([], $file)[0]; // sheet1
            $header = array_map('trim', $rows[0]);
            $dataRows = array_slice($rows, 1);
        } else {
            // Đọc CSV như cũ
            $handle = fopen($file->getRealPath(), 'r');
            $header = fgetcsv($handle);
            $dataRows = [];
            while (($row = fgetcsv($handle)) !== false) {
                $dataRows[] = $row;
            }
            fclose($handle);
        }

        $results = [
            'created' => [],
            'updated' => [],
            'errors' => [],
        ];

        DB::beginTransaction();
        try {
            foreach ($dataRows as $row) {
                $data = array_combine($header, $row);

                // Parse fields
                $data['size'] = $data['size'] ? json_decode($data['size'], true) : [];
                $data['options'] = $data['options'] ? explode(',', $data['options']) : [];
                $data['toppings'] = $data['toppings'] ? explode(',', $data['toppings']) : [];

                // Validate each row (basic)
                $rowValidator = Validator::make($data, [
                    'name' => 'required|string|max:255',
                    'description' => 'required|string',
                    'price' => 'required|numeric|min:0',
                    'image' => 'required|string',
                    'category_id' => 'required|exists:categories,id',
                    'stock' => 'required|integer|min:0',
                    'discount' => 'nullable|numeric|min:0',
                ]);
                if ($rowValidator->fails()) {
                    $results['errors'][] = [
                        'row' => $data,
                        'errors' => $rowValidator->errors()->all(),
                    ];
                    continue;
                }

                // Create or update
                if (!empty($data['id'])) {
                    $result = $this->updateOrCreateProduct($data, true);
                    $results['updated'][] = $result;
                } else {
                    $result = $this->updateOrCreateProduct($data, false);
                    $results['created'][] = $result;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }

    /**
     * Export products to CSV (streamed response)
     */
    public function exportProductsCsv($params)
    {
        $fileName = 'products_export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($params) {
            $handle = fopen('php://output', 'w');
            // Thêm BOM UTF-8 để Excel nhận đúng tiếng Việt
            fwrite($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            // Header
            fputcsv($handle, [
                'id',
                'name',
                'description',
                'price',
                'image',
                'category_name',
                'is_active',
                'stock',
                'discount',
                'size',
                'options',
                'toppings'
            ]);
            // Data
            $products = $this->getAllProductsForExport($params);
            foreach ($products as $product) {
                $transformed = $product->transform();
                // options: name:value,name:value,...
                $options = collect($transformed['options'])->map(function ($option) {
                    $values = collect($option['values'])->map(function ($v) {
                        return $v['value'];
                    })->implode('|');
                    return $option['name'] . ($values ? (':' . $values) : '');
                })->implode(',');
                // toppings: name|price,name|price,...
                $toppings = collect($transformed['toppings'])->map(function ($topping) {
                    return $topping['name'] . (isset($topping['price']) ? ('|' . $topping['price']) : '');
                })->implode(',');
                fputcsv($handle, [
                    $transformed['id'],
                    $transformed['name'],
                    $transformed['description'],
                    $transformed['price'],
                    $transformed['image'],
                    $transformed['category_name'],
                    $product->is_active,
                    $transformed['stock'],
                    $transformed['discount'],
                    json_encode($transformed['size']),
                    $options,
                    $toppings,
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Excel template for import (3 sheets)
     */
    public function exportImportTemplateExcel()
    {
        // Sheet 1: Template Import
        $sheet1Header = [
            'name',
            'description',
            'price',
            'image',
            'category_id',
            'stock',
            'discount',
            'size',
            'options',
            'toppings'
        ];
        $sheet1Sample = [
            'Trà sữa truyền thống',
            'Trà sữa vị truyền thống',
            '25000',
            'https://example.com/image.jpg',
            '1',
            '100',
            '10',
            '[{"label":"S","price":20000},{"label":"M","price":25000}]',
            '1,2',
            '1,3'
        ];

        // Sheet 2: List Topping
        $toppings = \App\Models\Topping::all(['id', 'name', 'price']);
        $sheet2Data = [['id', 'name', 'price']];
        foreach ($toppings as $topping) {
            $sheet2Data[] = [$topping->id, $topping->name, $topping->price];
        }

        // Sheet 3: List Option
        $options = \App\Models\Option::with('values')->get();
        $sheet3Data = [['id', 'name', 'type', 'is_required', 'values']];
        foreach ($options as $option) {
            $values = $option->values->pluck('value')->implode(', ');
            $sheet3Data[] = [
                $option->id,
                $option->name,
                $option->type,
                $option->is_required ? 'Yes' : 'No',
                $values
            ];
        }

        // Sheet 4: List Category
        $categories = \App\Models\Category::all(['id', 'name']);
        $sheet4Data = [['id', 'name']];
        foreach ($categories as $category) {
            $sheet4Data[] = [$category->id, $category->name];
        }

        // Tạo file Excel nhiều sheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        // Sheet 1
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('template import');
        $sheet1->fromArray($sheet1Header, null, 'A1');
        $sheet1->fromArray($sheet1Sample, null, 'A2');
        // Style header sheet1
        $headerCellCount1 = count($sheet1Header);
        $headerRange1 = 'A1:' . chr(64 + $headerCellCount1) . '1';
        $sheet1->getStyle($headerRange1)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
        ]);
        // Border all cells sheet1
        $sheet1->getStyle('A1:' . chr(64 + $headerCellCount1) . '2')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        // Sheet 2
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('list topping');
        $sheet2->fromArray($sheet2Data, null, 'A1');
        $headerCellCount2 = count($sheet2Data[0]);
        $rowCount2 = count($sheet2Data);
        $headerRange2 = 'A1:' . chr(64 + $headerCellCount2) . '1';
        $sheet2->getStyle($headerRange2)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
        ]);
        $sheet2->getStyle('A1:' . chr(64 + $headerCellCount2) . $rowCount2)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        // Sheet 3
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('list option');
        $sheet3->fromArray($sheet3Data, null, 'A1');
        $headerCellCount3 = count($sheet3Data[0]);
        $rowCount3 = count($sheet3Data);
        $headerRange3 = 'A1:' . chr(64 + $headerCellCount3) . '1';
        $sheet3->getStyle($headerRange3)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
        ]);
        $sheet3->getStyle('A1:' . chr(64 + $headerCellCount3) . $rowCount3)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Sheet 3
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle('list categories');
        $sheet4->fromArray($sheet4Data, null, 'A1');
        $headerCellCount4 = count($sheet4Data[0]);
        $rowCount4 = count($sheet4Data);
        $headerRange4 = 'A1:' . chr(64 + $headerCellCount4) . '1';
        $sheet4->getStyle($headerRange4)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
        ]);
        $sheet4->getStyle('A1:' . chr(64 + $headerCellCount4) . $rowCount4)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Xuất file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'products_import_template.xlsx';
        // Output to browser
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}