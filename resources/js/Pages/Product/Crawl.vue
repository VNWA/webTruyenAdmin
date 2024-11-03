<template>
    <div>
        <Loading v-if="isLoading" />
        <AppLayout title="Crawl Manga">
            <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight ">
                    Crawl Manga
                </h2>
            </template>
            <div class="py-5">
                <div class="w-full h-full flex items-center justify-center ">
                    <div class="border p-4 bg-white overflow-hidden shadow-xl min-w-[500px]">
                        <form @submit.prevent="submit" class=" sm:rounded-lg p-3 mb-3 p">
                            <div class="mb-3">
                                <label for="name"
                                    class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Nhập
                                    URL manga18fx</label>
                                <input type="text" v-model="form.url" id="name"
                                    class="bg-gray-50 border text-base border-gray-300 text-gray-900  rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-white-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="category_id"
                                    class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Danh
                                    mục</label>
                                <select v-model="form.category_id" id="category_id"
                                    class="bg-gray-50 border text-base border-gray-300 text-gray-900  rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-white-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required>
                                    <option v-for="(item, index) in page.props.categories" :value="item.id">
                                        {{ item.name }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="name"
                                    class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Chọn quốc
                                    gia</label>
                                <select v-model="form.nation_id" required
                                    class="bg-gray-50 border text-base border-gray-300 text-gray-900  rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-white-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-purple-500 dark:focus:border-purple-500">
                                    <option value="">Chọn quốc gia</option>
                                    <option v-for="item in page.props.nations" :value="item.id">{{ item.name }}</option>
                                </select>
                            </div>




                            <div class="mb-3">
                                <label for="name"
                                    class="block mb-2 font-bold text-base text-gray-900 dark:text-dark">Chọn thể
                                    loại</label>
                                <Multiselect v-model="form.types" mode="tags" :close-on-select="false"
                                    :searchable="true" :create-option="true" :options="page.props.types" />

                            </div>
                            <div class="flex items-center justify-center">

                                <button class="text-white font-bold bg-purple-500 px-3 text-lg">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AppLayout>

    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Multiselect from '@vueform/multiselect';
import Loading from '@/Components/Loading.vue';

const isLoading = ref(false)
const page = usePage()
const form = reactive({
    url: '',
    category_id: null,
    nation_id: null,
    types: []
});

const submit = async () => {
    isLoading.value = true;
    await axios.post(route('Product.Crawl.Import'), form)
        .then((response) => {
            toast.success(response.data.message)
        })
        .catch((error) => {
            console.log(error);
        });
    isLoading.value = false;

}
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
