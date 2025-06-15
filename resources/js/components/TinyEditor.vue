<template>
    <div>
        <label
            v-if="label"
            class="block text-sm font-medium text-gray-700 mb-1"
        >
            {{ label }}
        </label>
        <editor
            v-model="content"
            api-key="jbjcx2zx88zklgj5w7t3zh9dw4nnyzyry1erkzaoadxai94k"
            :init="editorConfig"
            @onChange="handleChange"
        />
    </div>
</template>

<script setup>
import Editor from "@tinymce/tinymce-vue";
import { ref, watch, onMounted, computed } from "vue";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    label: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue"]);

const content = ref(props.modelValue);

const editorConfig = computed(() => ({
    height: 500,
    menubar: true,
    plugins: [
        "advlist",
        "autolink",
        "lists",
        "link",
        "image",
        "charmap",
        "preview",
        "anchor",
        "searchreplace",
        "visualblocks",
        "code",
        "fullscreen",
        "insertdatetime",
        "media",
        "table",
        "help",
        "wordcount",
    ],
    toolbar:
        "undo redo | blocks | " +
        "bold italic forecolor | alignleft aligncenter " +
        "alignright alignjustify | bullist numlist outdent indent | " +
        "removeformat | help",
    content_style:
        "body { font-family:Helvetica,Arial,sans-serif; font-size:14px }",
    encoding: "xml",
    entity_encoding: "raw",
    convert_urls: false,
    relative_urls: false,
    remove_script_host: false,
    document_base_url: "http://localhost:8000",
}));

watch(
    () => props.modelValue,
    (newValue) => {
        if (newValue !== content.value) {
            content.value = newValue;
        }
    }
);

watch(content, (newValue) => {
    emit("update:modelValue", newValue);
});

const handleChange = (e) => {
    emit("update:modelValue", e.target.getContent());
};

onMounted(() => {
    if (!content.value && props.modelValue) {
        content.value = props.modelValue;
    }
});
</script>
