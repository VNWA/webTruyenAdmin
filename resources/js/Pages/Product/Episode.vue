<template>
    <AppLayout title="Danh sách tập của truyện ">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Danh sách tập của truyện
            </h2>
            <div class="flex justify-between items-center">
                <div class="flex">
                    <img :src="$page.props.product.url_avatar" alt="vinawebapp.com"
                        class="w-20 h-auto mr-3 xl:block hidden">
                    <div>
                        <span class=" block text-sm font-bold">{{ $page.props.product.name }}</span>
                        <span class=" block text-sm font-bold text-black/50">{{ $page.props.product.full_name
                            }}</span>
                    </div>
                </div>

                <div class="flex items-center">
                    <span class="font-bold text-lg">Thêm tập truyện : </span>
                    <input type="text" v-model="episodeCreateName" placeholder="Nhập tên tập truyện" class="mx-5" />
                    <button @click="createEpisode($page.props.product.id)"
                        class="px-3 py-2 bg-green-500 text-black/60 hover:bg-green-500/70">Thêm</button>

                </div>
            </div>
        </template>
        <div class="bg-white text-black/80 px-5  mb-6">
            <div class="">
                <div class="mb-3">
                    <div class="">

                        <div class="my-5 flex  items-center justify-start gap-6">
                            <span class="font-bold text-lg">Import Tập truyện (file zip, tên tập là tên file) :
                            </span>
                            <label for="files" class="bg-gray-500 px-5 py-3  text-white  my-5 cursor-pointer">
                                <input id="files" type="file" @change="readFiles" multiple accept=".zip"
                                    class="hidden" />
                                <span>Import Files
                                    ({{ files.length }})</span>
                            </label>
                            <div>
                                <button v-if="files.length > 0"
                                    class="bg-purple-500 px-5 py-3 rounded-lg text-white  my-5 cursor-pointer font-bold"
                                    @click="uploadFiles">
                                    Upload Files
                                </button>
                            </div>
                        </div>

                        <div v-if="files.length > 0" class="border border-black border-solid p-5">
                            <div>
                                <draggable v-model="files" :item-key="customKeyFunction" tag="ul"
                                    class="grid grid-cols-12 gap-4">
                                    <template #item="item">
                                        <li class="col-span-3 p-3 ButtonMutipleImage_item border">
                                            <span>{{ item.index + 1 }}. {{ item.element.name }}</span>
                                        </li>
                                    </template>
                                </draggable>

                            </div>

                        </div>


                    </div>
                </div>
                <div class="mb-2">
                    <div class="flex items-center justify-end">
                        <span class="me-3">Tìm kiếm :</span>

                        <input type="text" v-model="serverOptions.name" placeholder="Tìm kiếm">
                    </div>
                    <div>
                        <button :disabled="itemsSelected.length <= 0"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-2 rounded mr-4 text-xs"
                            @click="showModalDeleteMutipleItem">
                            <icon :icon="['fas', 'x']" class="mr-1" /> Xóa dữ liệu chọn
                        </button>
                    </div>
                </div>

                <DataTable :key="reRender" v-model:server-options="serverOptions" :headers="headers" :items="items"
                    :server-items-length="serverItemsLength" :loading="isTableLoading" buttons-pagination show-index
                    v-model:items-selected="itemsSelected">
                    <template #expand="item">
                        <div class="p-4  px-3 bg-neutral-950 text-white">
                            <div class="w-full py-3 text-xl text-white/90 uppercase font-bold text-center">
                                Danh sách server {{ item.name }}
                            </div>
                            <div class="bg-white/90 text-black">
                                <div class="px-3 py-2">
                                    <div class="py-2 px-5 flex items-center justify-end">
                                        <select v-model="serverCreateType">
                                            <option value="">Chọn kiểu import</option>
                                            <option value="import">Import File Zip</option>
                                            <option value="links">Nhập Links</option>
                                        </select>
                                        <button v-if="serverCreateType" @click="serverCreateItem(item.id)"
                                            class="bg-green-800 border-0 ms-3 text-white/80 hover:text-white uppercase px-3 py-2">Thêm
                                            server </button>
                                    </div>

                                    <div v-if="serverCreateType == 'links'">
                                        <textarea v-model="serverCreateTextarea"
                                            placeholder="Nhập link, ngăn cách nhau bằng cách xuống dòng" rows="5"
                                            class="w-full"></textarea>
                                    </div>
                                    <div v-else-if="serverCreateType == 'import'">
                                        <ButtonMutipleImage :ref="'images_' + item.id" />

                                    </div>
                                </div>
                                <div class="px-10 pt-10  border-2 border-solid border-black bg-white pb-3">
                                    <table class="w-full text-lg">
                                        <thead>
                                            <tr>
                                                <th class="border-2 border-solid border-black/50 text-start">
                                                    Server
                                                </th>
                                                <th class="border-2 border-solid border-black/50 text-start">
                                                    Link
                                                    Ảnh</th>
                                                <th class="border-2 border-solid border-black/50">Xóa</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="border-2 border-solid border-black/50"
                                                v-for="  (server, n)  in item.servers " :key="n">
                                                <td class="border-2 border-solid border-black/50 p-2 text-center">
                                                    Sever {{ n + 1 }}
                                                </td>
                                                <td class="border-2 border-solid border-black/50 px-2">
                                                    <ul class="py-3">
                                                        <li class="mb-3" v-for="(item) in server.images">
                                                            <a :href="item" class="hover:text-blue-500" target="_blank"
                                                                rel="noopener noreferrer">
                                                                {{ item }}
                                                            </a>

                                                        </li>
                                                    </ul>

                                                </td>
                                                <td class="border-2 border-solid border-black/50 p-2 text-center">
                                                    <button @click="serverDeleteItem(server.id)"
                                                        class="bg-red-500 text-white px-2  py-2">Xóa</button>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>


                        </div>
                    </template>
                    <template #item-name="{ id, name }">
                        <div class="py-3 flex items-center justify-start">

                            <span class=" block text-sm font-bold">{{ name }}</span>

                        </div>
                    </template>





                    <template #item-operation="{ id, name }">
                        <div class="py-3 flex items-center justify-center">
                            <button class="bg-red-600 text-white px-2 py-1 rounded-md mr-5"
                                @click="showModalDeleteItem(id, name)">
                                <icon :icon="['fas', 'x']" />
                            </button>
                            <button @click="showModalUpdateEpisode(id, name)"
                                class="bg-yellow-600 text-white px-2 py-1 rounded-md mr-5">
                                <icon :icon="['fas', 'pen-to-square']" />
                            </button>
                        </div>
                    </template>



                </DataTable>

            </div>
        </div>

        <DialogModal :show="modalDelete" @close="modalDelete = !modalDelete">

            <template #title>
                Xóa dữ liệu
            </template>

            <template #content>
                Chắc chắn xóa các dữ liệu này!
                <div class="mt-4">
                </div>
                <div v-if="itemsDelete.length > 0">
                    <div class="flex items-center" v-for=" item in itemsDelete ">
                        <icon :icon="['fas', 'x']" class="text-red-600 mr-1" /> <span>{{ item.name }}</span>
                    </div>
                </div>

                <div class="mt-3 mb-1">
                    <div class="text-xs text-gray-600">Lưu ý :
                        <ul class="pl-4">
                            <li class=" font-bold list-disc" style="font-family: Arial, Helvetica, sans-serif;">Các dữ
                                liệu được xóa sẽ tự động đưa vào thùng rác </li>
                            <li class=" font-bold list-disc" style="font-family: Arial, Helvetica, sans-serif;">Các dữ
                                liệu trong thùng rác được tự động xóa sau 30 </li>
                            <li class=" font-bold list-disc" style="font-family: Arial, Helvetica, sans-serif;">Muốn xóa
                                trực tiếp hãy bỏ chọn checkbox bên dưới</li>
                        </ul>
                    </div>

                </div>
            </template>

            <template #footer>


                <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-2 rounded mr-4 text-xs"
                    @click="deleteItems">
                    Xóa dữ liệu
                </button>
                <button class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-2 rounded mr-4 text-xs"
                    @click="modalDelete = !modalDelete">
                    Hủy lệnh
                </button>
            </template>
        </DialogModal>
        <DialogModal :show="isEditEpisode" @close="isEditEpisode = !isEditEpisode">

            <template #title>
                Cập nhập dữ liệu
            </template>

            <template #content>
                <form @submit.prevent="handleUpdateEpisode">

                    <div class="py-5 text-center">
                        <input v-model="episodeUpdate.name" type="text" class="border rounded-md w-full h-full">
                        <button
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-2 rounded mr-4 text-xs mt-5"
                            type="submit">
                            Cập nhập dữ liệu
                        </button>
                    </div>
                </form>
            </template>

            <template #footer>



                <button class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-2 rounded mr-4 text-xs"
                    @click="isEditEpisode = false">
                    Hủy lệnh
                </button>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch, getCurrentInstance, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import axios from 'axios';
import ButtonMutipleImage from '@/Components/ButtonMutipleImage.vue';
import draggable from 'vuedraggable';
const isEditEpisode = ref(false)
const page = usePage()
const episodeUpdate = reactive({
    id: null,
    name: ''
})
const showModalUpdateEpisode = (id, name) => {
    isEditEpisode.value = true;
    episodeUpdate.id = id;
    episodeUpdate.name = name;
}
const handleUpdateEpisode = async () => {
    try {
        const response = await axios.post(route('Episode.Update', [page.props.product.id, episodeUpdate.id]), episodeUpdate,);
        loadFromServer();
        isEditEpisode.value = false;

        toast.success('Update thành công!');
    } catch (error) {
        console.error('Lỗi khi upload:', error);
        toast.error('Lỗi!');

    }
}


const { proxy } = getCurrentInstance();
const files = ref([]);
const customKeyFunction = (item) => {
    return item.someUniqueIdentifier;

}
const resetFiles = () => {
    files.value = []; // Reset mảng files về mảng rỗng
    const inputFile = document.querySelector('input[type="file"]'); // Chọn input file
    if (inputFile) {
        inputFile.value = ''; // Reset giá trị input file
    }
};
// Đọc các file đã chọn
const readFiles = (event) => {
    const selectedFiles = Array.from(event.target.files);

    // Thêm các file đã chọn vào mảng `files.value`
    selectedFiles.forEach((file, index) => {
        files.value.push({
            file,
            name: file.name,
            order: files.value.length + 1, // Đảm bảo thứ tự tiếp tục tăng
        });
    });
    console.log(files.value)
};


// Upload các file qua API
const uploadFiles = async () => {
    console.log(page.props.product.id)
    if (files.value.length === 0) {
        toast.success('Chọn ít nhất 1 file zip');

        return;
    }

    const formData = new FormData();
    files.value.forEach(({ file }) => {
        formData.append('files[]', file);
    });
    try {
        const response = await axios.post(route('Episode.Import', page.props.product.id), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'Accept': 'application/json',
            },
        });
        loadFromServer();
        resetFiles();
        toast.success('Upload thành công!');
    } catch (error) {
        console.error('Lỗi khi upload:', error);
        toast.success('Lỗi!');

    }
};


const isTableLoading = ref(false)
const isModal = ref(false)
const items = ref([])
const reRender = ref(1);
const serverItemsLength = ref(0);
const serverOptions = ref({
    page: 1,
    rowsPerPage: 10,
    sortBy: 'updated_at',
    sortType: 'desc',
    name: '',
});

watch(serverOptions, () => {
    loadFromServer();
}, { deep: true });

const restApiUrl = computed(() => {
    const { page, rowsPerPage, sortBy, sortType, name } = serverOptions.value;
    let url = window.location.href + `/load-data-table?page=${page}&per_page=${rowsPerPage}&sortBy=${sortBy}&sortType=${sortType}`;
    if (name) {
        url += `&name=${name}`
    }
    return url;
});

const loadFromServer = async () => {
    isTableLoading.value = true;
    reRender.value++;
    try {
        const response = await axios.get(restApiUrl.value);
        items.value = response.data.data;
        serverItemsLength.value = response.data.total;
    } catch (error) {
        console.error(error);
    } finally {
        isTableLoading.value = false;
    }

};

onMounted(() => {
    loadFromServer();
})

const serverDeleteItem = (id) => {
    if (confirm("Xác nhận xóa server này ?")) {
        axios.post('/delete-items', {
            tb: 'servers',
            dataId: [id],
            trash: false,
        }).then((response) => {
            loadFromServer();

        })
    }

}

const episodeCreateName = ref('');
const createEpisode = (id) => {

    axios.post(window.location.href + '/create', {
        name: episodeCreateName.value
    })
        .then((response) => {
            if (response.data.error) {
                toast.error(response.data.error, {
                    autoClose: 3000,
                });

            } else {
                loadFromServer()
                toast.success("Uploads dữ liệu thành công", {
                    autoClose: 1000,
                });
            }
        })
        .catch((error) => {
            console.log(error);
        });
}
const serverCreateType = ref('')
const serverCreateTextarea = ref('')
const serverCreateItem = (id) => {
    const images = ref([]);
    if (serverCreateType.value == 'import') {
        const imagesRef = proxy.$refs['images_' + id]; // Lấy component theo ref
        if (imagesRef) {
            images.value = imagesRef.list_image; // Giả sử list_image là dữ liệu bạn muốn lấy
        }


    } else if (serverCreateType.value == 'links') {
        const textareaContent = serverCreateTextarea.value;

        images.value = textareaContent.split('\n').map(link => link.trim()).filter(link => link !== '');

    } else {
        return false;
    }
    axios.post(window.location.href + '/server/' + id + '/create', {
        images: images.value
    })
        .then((response) => {
            if (response.data.error) {
                toast.error(response.data.error, {
                    autoClose: 3000,
                });

            } else {
                loadFromServer();

            }
        })
        .catch((error) => {
            console.log(error);
        });
}



const checkboxDeleteToTrash = ref(false);
const itemsDelete = ref([])
const modalDelete = ref(false)
const itemsSelected = ref([])
const headers = [
    { text: "Tên dữ liệu", value: "name" },
    { text: "Hành động", value: "operation" },
];
const deleteItems = async () => {

    const dataDelete = [];
    itemsDelete.value.forEach(element => {
        dataDelete.push(element.id)

    });
    console.log(dataDelete)
    try {
        const response = await axios.post('/delete-items', {
            tb: 'episodes',
            dataId: dataDelete,
            trash: false,
        });

        loadFromServer()
        modalDelete.value = false;
        toast.success("Xóa dữ liệu thành công", {
            autoClose: 1500,
        });

    } catch (error) {
        console.error('Error while changing status:', error);
    }
}
const showModalDeleteMutipleItem = () => {

    itemsDelete.value = [];
    itemsSelected.value.forEach(element => {
        itemsDelete.value.push({ id: element.id, name: element.name })

    });
    modalDelete.value = true;

}
const showModalDeleteItem = (deleteId, deleteName) => {
    itemsDelete.value = [];
    itemsDelete.value.push({ id: deleteId, name: deleteName })
    modalDelete.value = true;

}


</script>
