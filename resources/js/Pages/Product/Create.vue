

<template>
    <AppLayout title="Thêm  Truyện">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight ">
                Thêm Truyện
            </h2>
        </template>
        <div class="overflow-hidden  px-5 mb-3 ">
            <div class="w-100  p-1 flex justify-between items-center">
                <div class="  ">
                    <Link :href="route('Product')" class="text-dark-700 hover:text-purple-900 font-bold underline decoration-1">
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
                    <div class="col-span-5 p-2">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-3 mb-3 p">

                            <div class="mb-3">
                                <label for="name" class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Tên truyện</label>
                                <input type="text" v-model="name" @input="updateSlug" @change="setDataMetaTitle" placeholder="John" id="name" class="bg-gray-50 border text-base border-gray-300 text-gray-900  rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-white-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-purple-500 dark:focus:border-purple-500" required>
                                <span class="text-red-600 text-xs" ref="scroll_name">{{ error.name }}</span>
                                <input type="text" v-model="slug" class=" mt-3 bg-gray-50 border text-base border-gray-300 text-gray-900   w-full " disabled>
                                <span class="text-red-600 text-xs" ref="scroll_slug">{{ error.slug }}</span>
                            </div>
                            <div class=" mb-3">
                                <label for="full_name" class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Tên đầy đủ</label>
                                <input type="text" v-model="full_name" @change="setDataMetaTitle" placeholder="Nhập tên đầy đủ của truyện" id="full_name" class="bg-gray-50 border text-base border-gray-300 text-gray-900  rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-white-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-purple-500 dark:focus:border-purple-500" required>
                                <span class="text-red-600 text-xs" ref="scroll_full_name">{{ error.full_name }}</span>

                            </div>
                            <div class="mb-3">
                                <label for="desc" class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Mô tả dữ liệu</label>
                                <Editer ref="desc" />

                                <span class="text-red-600 text-xs" ref="scroll_desc">{{ error.desc }}</span>


                            </div>
                        </div>
                    </div>
                    <div class="col-span-3 p-2">
                        <div class="bg-white  shadow-xl sm:rounded-lg p-3 mb-3 p">
                            <div class="mb-3">
                                <label for="name" class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Chọn danh muc</label>
                                <select v-model="category_id" class="bg-gray-50 border text-base border-gray-300 text-gray-900  rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-white-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-purple-500 dark:focus:border-purple-500">
                                    <option value="">Chọn danh muc</option>
                                    <option v-for="item in dataCategory" :value="item.id">{{ item.name }}</option>
                                </select>
                                <span class="text-red-600 text-xs" ref="scroll_category_id">{{ error.category_id }}</span>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Chọn quốc gia</label>
                                <select v-model="nation_id" class="bg-gray-50 border text-base border-gray-300 text-gray-900  rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-white-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-purple-500 dark:focus:border-purple-500">
                                    <option value="">Chọn quốc gia</option>
                                    <option v-for="item in dataNation" :value="item.id">{{ item.name }}</option>
                                </select>
                                <span class="text-red-600 text-xs" ref="scroll_nation_id">{{ error.nation_id }}</span>
                            </div>




                            <div class="mb-3">
                                <label for="name" class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Chọn thể loại</label>
                                <SelectMutiple :options="dataType" ref="types" />
                                <span class="text-red-600 text-xs" ref="scroll_types">{{ error.types }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-4">
                        <div class="grid grid-cols-12">
                            <div class="xl:col-span-12 col-span-12 p-2">
                                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-3 mb-3 p">
                                    <div class="mb-3">
                                        <label for="name" class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Ảnh đại diện</label>
                                        <InputUrlImage ref="url_avatar" @child-changed="setDataMetaImage" />
                                        <span class="text-red-600 text-xs" ref="scroll_url_avatar">{{ error.url_avatar }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="xl:col-span-12 col-span-12 p-2">
                                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-3 mb-3 p">
                                    <div class="mb-3">
                                        <label for="name" class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Ảnh nền</label>
                                        <InputUrlImage ref="url_bg" />
                                        <span class="text-red-600 text-xs" ref="scroll_url_bg">{{ error.url_bg }}</span>

                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="xl:col-span-6 col-span-12 p-2">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg w-full p-3 mb-3 p">
                            <div class="mb-3">
                                <label for="name" class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Meta Image</label>
                                <InputUrlImage ref="meta_image" :data="dataMetaImage" />
                                <span class="text-red-600 text-xs" ref="scroll_meta_image">{{ error.meta_image }}</span>

                            </div>
                        </div>

                    </div>
                    <div class="xl:col-span-6 col-span-12 p-2">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2 mb-3">
                            <label for="editer" class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Thẻ Meta Title</label>
                            <input type="text" v-model="meta_title" placeholder="Nhập tiêu đề" id="name" class="bg-gray-50 border text-base border-gray-300 text-gray-900  rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-white-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-purple-500 dark:focus:border-purple-500" required>
                            <span class="text-red-600 text-xs" ref="scroll_meta_title">{{ error.meta_title }}</span>

                        </div>
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2 mb-3">
                            <label for="editer" class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Thẻ Meta Description</label>
                            <input type="text" v-model="meta_desc" placeholder="Nhập tiêu đề" id="name" class="bg-gray-50 border text-base border-gray-300 text-gray-900  rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-white-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-purple-500 dark:focus:border-purple-500" required>
                            <span class="text-red-600 text-xs" ref="scroll_meta_desc">{{ error.meta_desc }}</span>

                        </div>
                    </div>



                </div>

            </div>
        </div>


        <div class="bg-slate-900 overflow-hidden shadow-xl  p-2  ">
            <div class="w-100  p-1 flex justify-between items-center">
                <div class=" text-white ">
                    <Link :href="route('Product')" class="text-dark-700 hover:text-purple-900 font-bold underline decoration-1">
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
    </AppLayout>
</template>

<script>

import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import getSlug from 'speakingurl';

import InputUrlImage from '@/Components/InputUrlImage.vue';
import SelectMutiple from '@/Components/SelectMutiple.vue';
import ButtonMutipleImage from '@/Components/ButtonMutipleImage.vue';
import Editer from '@/Components/Editer.vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';



export default {

    name: 'Create  Project',
    components: {
        SelectMutiple, InputUrlImage, ButtonMutipleImage, Editer, AppLayout, Link
    },

    data() {
        return {
            dataMetaImage: '',

            nation_id: '',
            category_id:'',
            name: '',
            full_name: '',
            date: '',
            slug: '',
            meta_title: '',
            meta_desc: '',
            error: {
                category_id: '',
                nation_id: '',
                full_name: '',
                date: '',
                name: '',
                slug: '',
                desc: '',
                types: '',
                url_avatar: '',
                url_bg: '',
                types: '',
                meta_image: '',
                meta_title: '',
                meta_desc: '',
            },
        }
    },

    methods: {
        setDataMetaTitle() {

        },
        setDataMetaImage(childData) {
            this.dataMetaImage = childData;
        },

        clearErrors() {

            this.error.category_id = "";
            this.error.nation_id = "";
            this.error.full_name = "";
            this.error.date = "";
            this.error.name = "";
            this.error.slug = "";
            this.error.desc = "";
            this.error.types = "";
            this.error.url_avatar = "";
            this.error.url_bg = "";
            this.error.types = "";
            this.error.meta_image = "";
            this.error.meta_title = "";
            this.error.meta_desc = "";
        },
        scrollToElement(nameElement) {
            // Sử dụng `ref` để lấy tham chiếu đến phần tử
            const element = this.$refs['scroll_' + nameElement];
            // Kiểm tra xem phần tử có tồn tại không
            if (element) {
                // Lấy kích thước của phần tử và cửa sổ trình duyệt
                const elementRect = element.getBoundingClientRect();
                const windowHeight = window.innerHeight || document.documentElement.clientHeight;

                // Tính toán vị trí cuộn sao cho phần tử nằm giữa màn hình
                const scrollPosition = elementRect.top - (windowHeight / 2 - elementRect.height / 2);

                // Cuộn tới phần tử
                window.scrollTo({
                    top: scrollPosition,
                    behavior: 'smooth',
                });
            }
        },
        updateSlug() {
            // Chuyển đổi chuỗi name thành slug
            this.slug = getSlug(this.name, { lang: 'vi' });
        },
        submitForm() {
            this.clearErrors();
            const category_id = this.category_id;
            const nation_id = this.nation_id;
            const types = this.$refs.types.dataSelectedOptions;

            const url_avatar = this.$refs.url_avatar.url_image;
            const url_bg = this.$refs.url_bg.url_image;


            const full_name = this.full_name;
            const date = this.date;
            const name = this.name;
            const slug = this.slug;
            const desc = this.$refs.desc.editer_data;


            const meta_title = this.meta_title;
            const meta_image = this.$refs.meta_image.url_image;
            const meta_desc = this.meta_desc;


            axios.post('', {
                category_id: category_id,
                nation_id: nation_id,
                types: types,
                url_avatar: url_avatar,
                url_bg: url_bg,
                full_name: full_name,
                date: date,
                name: name,
                slug: slug,
                desc: desc,
                meta_title: meta_title,
                meta_image: meta_image,
                meta_desc: meta_desc,
            })
                .then((response) => {
                    if (response.data.error) {
                        toast.error(response.data.error, {
                            autoClose: 3000,
                        });
                        // this.scrollToElement(response.data.column);
                        this.error[response.data.column] = response.data.error;

                    } else {
                        toast.success("Uploads dữ liệu thành công", {
                            autoClose: 1000,
                        });
                    }

                })
                .catch((error) => {
                    console.log(error);
                });
        }
    },

    setup() {
        const dataCategory = ref([]);
        const dataNation = ref([]);
        const dataType = ref([]);

        const data = usePage();
        dataNation.value = data.props.listNation;
        dataCategory.value = data.props.listCategory;
        dataType.value = data.props.listType;

        return {
            dataCategory, dataNation, dataType
        }
    },


}
</script>

