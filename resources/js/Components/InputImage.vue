<template>
    <div class="flex items-center w-full mb-3">
        <div class="relative w-full mr-3 formkit-field">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                <icon :icon="['fas', 'image']" />
            </div>
            <input
                id="inputImage"
                :value="modelValue"
                @input="updateValue"
                class="formkit-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                aria-label="Đường dẫn ảnh"
                placeholder="Đường dẫn ảnh"
                required
                type="text"
            />
        </div>
        <button
            @click="openCKFinder"
            style="min-width: 120px;"
            class="px-5 py-3 w-auto text-sm font-medium text-center text-white bg-blue-700 rounded-lg cursor-pointer hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
        >
            <icon :icon="['fas', 'circle-down']" /> Chọn ảnh
        </button>
    </div>
    <div class="border rounded p-4" v-if="modelValue">
        <img :src="modelValue" width="100%" height="auto" class="w-full h-auto" alt="Ảnh đã chọn" />
    </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, onMounted } from 'vue';

const props = defineProps({
    modelValue: String,  // Nhận giá trị từ prop
});

const emit = defineEmits(['update:modelValue']);  // Phát ra sự kiện update:modelValue khi giá trị thay đổi

// Hàm để cập nhật giá trị khi người dùng thay đổi
const updateValue = (event) => {
    emit('update:modelValue', event.target.value);  // Phát sự kiện cập nhật giá trị
};

const openCKFinder = () => {
    CKFinder.popup({
        chooseFiles: true,
        onInit: function (finder) {
            finder.on('files:choose', function (evt) {
                const file = evt.data.files.first();
                const imageUrl = file.getUrl();
                emit('update:modelValue', imageUrl);  // Cập nhật URL ảnh
            });
        }
    });
};

const input = ref(null);

onMounted(() => {
    if (input.value && input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>
