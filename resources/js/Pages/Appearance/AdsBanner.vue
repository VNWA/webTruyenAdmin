<template>
    <div>
        <AppLayout title="ADS Banner">
            <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    ADS Banner
                </h2>
            </template>
            <div class="pb-12 pt-4">
                <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
                    <div class="sticky top-0 left-0 text-end mb-3">
                        <button type="button" @click="update"
                            class="text-white bg-gradient-to-r from-sky-300 via-purple-500 to-purple-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-purple-700  shadow-lg shadow-purple-500/50 dark:shadow-lg  font-medium rounded-lg text-sm px-5 py-2.5 text-center ">
                            <icon :icon="['fas', 'save']" class="mr-2" /> Lưu Dữ Liệu
                        </button>
                    </div>
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-3 mb-5 d-block">

                        <div class="">
                            <h3 class="block mb-2 font-bold text-base  text-gray-900 dark:text-dark capitalize">
                                Top Home
                            </h3>
                            <div>
                                <div class="flex items-center justify-start gap-2 mb-5">
                                    <input type="checkbox" id="topHomeIsImage" :true-value="0" :false-value="1"
                                        v-model="form.top_home.isImage">
                                    <label for="topHomeIsImage">Nhúng Iframe ?</label>
                                </div>
                                <div v-if="form.top_home.isImage == 0">

                                    <TextArea v-model="form.top_home.iframe" cols="30" rows="3"
                                        placeholder="Nhúng iframe"></TextArea>
                                </div>
                                <div v-else>
                                    <div class="mb-3">
                                        <InputLabel value="Link" for="topHomeLink" />
                                        <TextInput v-model="form.top_home.link" id="topHomeLink" />
                                    </div>
                                    <div class="mb-3">
                                        <InputLabel value="Image" for="topHomeImage" />
                                        <InputImage v-model="form.top_home.image" />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-3 mb-5 d-block">

                        <div class="">
                            <h3 class="block mb-2 font-bold text-base  text-gray-900 dark:text-dark capitalize">
                                Top Raw
                            </h3>
                            <div>
                                <div class="flex items-center justify-start gap-2 mb-5">
                                    <input type="checkbox" id="topRawIsImage" :true-value="0" :false-value="1"
                                        v-model="form.top_raw_manga.isImage">
                                    <label for="topRawIsImage">Nhúng Iframe ?</label>
                                </div>
                                <div v-if="form.top_raw_manga.isImage == 0">

                                    <TextArea v-model="form.top_raw_manga.iframe" cols="30" rows="3"
                                        placeholder="Nhúng iframe"></TextArea>
                                </div>
                                <div v-else>
                                    <div class="mb-3">
                                        <InputLabel value="Link" for="topRawLink" />
                                        <TextInput v-model="form.top_raw_manga.link" id="topRawLink" />
                                    </div>
                                    <div class="mb-3">
                                        <InputLabel value="Image" for="topRawImage" />
                                        <InputImage v-model="form.top_raw_manga.image" />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-3 mb-5 d-block">

                        <div class="">
                            <h3 class="block mb-2 font-bold text-base  text-gray-900 dark:text-dark capitalize">
                                Top Sub Manga
                            </h3>
                            <div>
                                <div class="flex items-center justify-start gap-2 mb-5">
                                    <input type="checkbox" id="topSubIsImage" :true-value="0" :false-value="1"
                                        v-model="form.top_sub_manga.isImage">
                                    <label for="topSubIsImage">Nhúng Iframe ?</label>
                                </div>
                                <div v-if="form.top_sub_manga.isImage == 0">

                                    <TextArea v-model="form.top_sub_manga.iframe" cols="30" rows="3"
                                        placeholder="Nhúng iframe"></TextArea>
                                </div>
                                <div v-else>
                                    <div class="mb-3">
                                        <InputLabel value="Link" for="topSubLink" />
                                        <TextInput v-model="form.top_sub_manga.link" id="topSubLink" />
                                    </div>
                                    <div class="mb-3">
                                        <InputLabel value="Image" for="topSubImage" />
                                        <InputImage v-model="form.top_sub_manga.image" />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-3 mb-5 d-block">

                        <div class="">
                            <h3 class="block mb-2 font-bold text-base  text-gray-900 dark:text-dark capitalize">
                                Top New Manga
                            </h3>
                            <div>
                                <div class="flex items-center justify-start gap-2 mb-5">
                                    <input type="checkbox" id="topNewIsImage" :true-value="0" :false-value="1"
                                        v-model="form.top_new_manga.isImage">
                                    <label for="topNewIsImage">Nhúng Iframe ?</label>
                                </div>
                                <div v-if="form.top_new_manga.isImage == 0">

                                    <TextArea v-model="form.top_new_manga.iframe" cols="30" rows="3"
                                        placeholder="Nhúng iframe"></TextArea>
                                </div>
                                <div v-else>
                                    <div class="mb-3">
                                        <InputLabel value="Link" for="topNewLink" />
                                        <TextInput v-model="form.top_new_manga.link" id="topNewLink" />
                                    </div>
                                    <div class="mb-3">
                                        <InputLabel value="Image" for="topNewImage" />
                                        <InputImage v-model="form.top_new_manga.image" />
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="">
                            <h3 class="block mb-2 font-bold text-base  text-gray-900 dark:text-dark capitalize">
                                Manga Info
                            </h3>
                            <div>
                                <div class="flex items-center justify-start gap-2 mb-5">
                                    <input type="checkbox" id="mangaInfoIsImage" :true-value="0" :false-value="1"
                                        v-model="form.product.isImage">
                                    <label for="mangaInfoIsImage">Nhúng Iframe ?</label>
                                </div>
                                <div v-if="form.product.isImage == 0">

                                    <TextArea v-model="form.product.iframe" cols="30" rows="3"
                                        placeholder="Nhúng iframe"></TextArea>
                                </div>
                                <div v-else>
                                    <div class="mb-3">
                                        <InputLabel value="Link" for="mangaInfoLink" />
                                        <TextInput v-model="form.product.link" id="mangaInfoLink" />
                                    </div>
                                    <div class="mb-3">
                                        <InputLabel value="Image" for="mangaInfoImage" />
                                        <InputImage v-model="form.product.image" />
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="">
                            <h3 class="block mb-2 font-bold text-base  text-gray-900 dark:text-dark capitalize">
                                Top Chapter
                            </h3>
                            <div>
                                <div class="flex items-center justify-start gap-2 mb-5">
                                    <input type="checkbox" id="episodeOneIsImage" :true-value="0" :false-value="1"
                                        v-model="form.episode_one.isImage">
                                    <label for="episodeOneIsImage">Nhúng Iframe ?</label>
                                </div>
                                <div v-if="form.episode_one.isImage == 0">

                                    <TextArea v-model="form.episode_one.iframe" cols="30" rows="3"
                                        placeholder="Nhúng iframe"></TextArea>
                                </div>
                                <div v-else>
                                    <div class="mb-3">
                                        <InputLabel value="Link" for="episodeOneLink" />
                                        <TextInput v-model="form.episode_one.link" id="episodeOneLink" />
                                    </div>
                                    <div class="mb-3">
                                        <InputLabel value="Image" for="episodeOneImage" />
                                        <InputImage v-model="form.episode_one.image" />
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="">
                            <h3 class="block mb-2 font-bold text-base  text-gray-900 dark:text-dark capitalize">
                                Middle Chapter
                            </h3>
                            <div>
                                <div class="flex items-center justify-start gap-2 mb-5">
                                    <input type="checkbox" id="episodeTwoIsImage" :true-value="0" :false-value="1"
                                        v-model="form.episode_two.isImage">
                                    <label for="episodeTwoIsImage">Nhúng Iframe ?</label>
                                </div>
                                <div v-if="form.episode_two.isImage == 0">

                                    <TextArea v-model="form.episode_two.iframe" cols="30" rows="3"
                                        placeholder="Nhúng iframe"></TextArea>
                                </div>
                                <div v-else>
                                    <div class="mb-3">
                                        <InputLabel value="Link" for="episodeTwoLink" />
                                        <TextInput v-model="form.episode_two.link" id="episodeTwoLink" />
                                    </div>
                                    <div class="mb-3">
                                        <InputLabel value="Image" for="episodeTwoImage" />
                                        <InputImage v-model="form.episode_two.image" />
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="">
                            <h3 class="block mb-2 font-bold text-base  text-gray-900 dark:text-dark capitalize">
                               End Chapter
                            </h3>
                            <div>
                                <div class="flex items-center justify-start gap-2 mb-5">
                                    <input type="checkbox" id="episodeThreeIsImage" :true-value="0" :false-value="1"
                                        v-model="form.episode_three.isImage">
                                    <label for="episodeThreeIsImage">Nhúng Iframe ?</label>
                                </div>
                                <div v-if="form.episode_three.isImage == 0">

                                    <TextArea v-model="form.episode_three.iframe" cols="30" rows="3"
                                        placeholder="Nhúng iframe"></TextArea>
                                </div>
                                <div v-else>
                                    <div class="mb-3">
                                        <InputLabel value="Link" for="episodeThreeLink" />
                                        <TextInput v-model="form.episode_three.link" id="episodeThreeLink" />
                                    </div>
                                    <div class="mb-3">
                                        <InputLabel value="Image" for="episodeThreeImage" />
                                        <InputImage v-model="form.episode_three.image" />
                                    </div>
                                </div>
                            </div>

                        </div>


                    </div>
                </div>
            </div>


        </AppLayout>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

import TextArea from '@/Components/TextArea.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputImage from '@/Components/InputImage.vue';

const form = ref({
    top_home: {
        iframe: '',
        image: '',
        link: '/',
        isImage: 0
    },
    top_raw_manga: {
        iframe: '',
        image: '',
        link: '/',
        isImage: 0
    },
    top_sub_manga: {
        iframe: '',
        image: '',
        link: '/',
        isImage: 0
    },
    top_new_manga: {
        iframe: '',
        image: '',
        link: '/',
        isImage: 0
    },
    product: {
        iframe: '',
        image: '',
        link: '/',
        isImage: 0
    },
    episode_one: {
        iframe: '',
        image: '',
        link: '/',
        isImage: 0
    }, episode_two: {
        iframe: '',
        image: '',
        link: '/',
        isImage: 0
    }
    , episode_three: {
        iframe: '',
        image: '',
        link: '/',
        isImage: 0
    }
})

const page = usePage();
form.value = page.props.data.value
console.log(form.value)
const update = async () => {
    await axios.post(route('AdsBanner.Update'), form.value)
        .then((response) => {
            toast.success(response.data.message)
        })
        .catch((error) => {
            console.error(error);
        });
}


</script>
