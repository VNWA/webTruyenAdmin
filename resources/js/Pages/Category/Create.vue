<template>
    <AppLayout title="Thêm Danh Mục">

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight ">
                Thêm Danh Mục
            </h2>
        </template>
        <div class="overflow-hidden  px-5 mb-3 ">
            <div class="w-100  p-1 flex justify-between items-center">
                <div class="">
                    <Link :href="route('Category')" class="text-dark-700 hover:text-purple-900 font-bold underline decoration-1">
                        <icon :icon="['fas', 'arrow-left']" /> Trở về
                    </Link>
                </div>
                <div class="">
                    <button type="button" @click="submitForm" class="text-gray-900 bg-gradient-to-r from-purple-200 via-purple-400 to-purple-500 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-purple-300 dark:focus:ring-purple-800 shadow-lg shadow-purple-500/50 dark:shadow-lg dark:shadow-purple-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center ">
                        <icon :icon="['fas', 'save']" class="mr-2" /> Lưu Dữ Liệu
                    </button>
                </div>
            </div>
        </div>
        <div class="pb-12">
            <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">

                <div class="grid grid-cols-12 relative">
                    <div class="col-span-6 p-2">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg w-full p-3 mb-3 p">
                            <div class="mb-3">
                                <label for="meta_image" class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Meta Image</label>
                                <InputUrlImage ref="meta_image" :data="data_meta_image" />
                                <span class="text-red-600 text-xs" ref="scroll_meta_image">{{ error.meta_image }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-6 p-2">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2 mb-3">
                            <label for="meta_title" class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Thẻ Meta Title</label>
                            <input type="text" v-model="meta_title" placeholder="Nhập tiêu đề" id="meta_title" class="bg-gray-50 border text-base border-gray-300 text-gray-900 rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-white-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-purple-500 dark:focus:border-purple-500" required>
                            <span class="text-red-600 text-xs" ref="scroll_meta_title">{{ error.meta_title }}</span>
                        </div>
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2 mb-3">
                            <label for="meta_desc" class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Thẻ Meta Description</label>
                            <input type="text" v-model="meta_desc" placeholder="Nhập mô tả" id="meta_desc" class="bg-gray-50 border text-base border-gray-300 text-gray-900 rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-white-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-purple-500 dark:focus:border-purple-500" required>
                            <span class="text-red-600 text-xs" ref="scroll_meta_desc">{{ error.meta_desc }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </AppLayout>
</template>

<script>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import getSlug from 'speakingurl';
import InputUrlImage from '@/Components/InputUrlImage.vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    name: 'Create Category Blog',
    components: {
        InputUrlImage, AppLayout, Link
    },

    data() {
        return {
            meta_title: '',
            data_meta_image: '',
            meta_desc: '',
            error: {
                meta_title: '',
                meta_image: '',
                meta_desc: '',
            }
        }
    },

    methods: {
        clearErrors() {
            this.error.meta_title = "";
            this.error.meta_image = "";
            this.error.meta_desc = "";
        },
        submitForm() {
            this.clearErrors();
            const meta_title = this.meta_title;
            const meta_image = this.$refs.meta_image.url_image;
            const meta_desc = this.meta_desc;
            axios.post(route('Category.store'), {
                meta_title: meta_title,
                meta_image: meta_image,
                meta_desc: meta_desc,
            })
                .then((response) => {
                    if (response.data.error) {
                        toast.error(response.data.error, {
                            autoClose: 3000,
                        });
                        this.error[response.data.column] = response.data.error;
                    } else {
                        toast.success("Tạo danh mục thành công", {
                            autoClose: 1000,
                        });
                        this.$inertia.visit(route('Category'));
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        }
    },
    setup() {
        const data = usePage();
        const data_meta_image = ref('');
        return {
            data_meta_image,
        }
    },
}
</script>
