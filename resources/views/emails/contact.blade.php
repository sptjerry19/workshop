@component('mail::message')
# Thông tin liên hệ mới

**Tên:** {{ $contact->name }}
**Email:** {{ $contact->email }}
**Số điện thoại:** {{ $contact->phone ?? 'Không có' }}
**Loại liên hệ:** {{ ucfirst($contact->type) }}

---

**Nội dung:**
{{ $contact->subject }}

@endcomponent
