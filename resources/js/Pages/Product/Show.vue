<template>
    <AppLayout title="Danh sách Truyện">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Danh sách Truyện
            </h2>
        </template>

        <div class="py-2">
            <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg pt-8 pb-12 px-2">
                    <div class="flex items-center justify-between gap-4 w-full">
                        <div class="">
                            <button :disabled="itemsSelected.length <= 0"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-2 rounded mr-4 text-xs"
                                @click="showModalDeleteMutipleItem">
                                <icon :icon="['fas', 'x']" class="mr-1" /> Xóa dữ liệu chọn
                            </button>
                        </div>
                        <div class=" text-xs uppercase flex justify-end gap-4">
                            <button class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-2 rounded"
                                @click="loadFromServer()">
                                <icon :icon="['fas', 'rotate-right']" class="mr-1" /> Load Lại Dữ liệu
                            </button>
                            <Link :href="route('Product.Crawl.Show')"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-2 rounded ">
                            <icon :icon="['fas', 'plus']" /> Crawl manga
                            </Link>
                            <Link :href="route('Product.Create')"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-2 rounded ">
                            <icon :icon="['fas', 'plus']" /> Thêm dữ liệu
                            </Link>
                        </div>
                    </div>


                    <div class="my-2 py-10">
                        <div class="mb-2">
                            <div class="flex items-center justify-end">
                                <span class="me-3">Tìm kiếm :</span>

                                <input type="text" v-model="serverOptions.name" placeholder="Tìm kiếm">
                            </div>

                        </div>

                        <DataTable :key="reRender" v-model:server-options="serverOptions" :headers="headers"
                            buttons-pagination :loading="isTableLoading" :items="items"
                            :server-items-length="serverItemsLength" show-index v-model:items-selected="itemsSelected">

                            <template #item-name="{ id, name, full_name, url_avatar }">
                                <div class="py-3 flex items-center justify-start">
                                    <img :src="url_avatar" alt="vinawebapp.com"
                                        class="w-20 h-auto mr-3 xl:block hidden">
                                    <div>
                                        <a target="_blank" :href="route('Product.Edit', id)">
                                            <span class=" block text-sm font-bold">{{ name }}</span>
                                            <span class=" block text-sm font-bold text-black/50">{{ full_name }}</span>
                                        </a>

                                    </div>
                                </div>
                            </template>

                            <template #item-status="{ id, status }">
                                <div class="flex items-center cursor-pointer">
                                    <input type="checkbox" :id="'statusCheckbox-' + id" class="hidden"
                                        @change="handleStatusChange(id, status)" />
                                    <label :for="'statusCheckbox-' + id" class="flex items-center cursor-pointer">
                                        <div class="relative">
                                            <div class="toggle-path bg-gray-300 w-9 h-5 rounded-full p-0">
                                                <div class="toggle-circle  w-5 h-5 rounded-full shadow-md"
                                                    :class="{ 'transform translate-x-full bg-purple-500': status == 1, 'bg-white': status == 0 }">
                                                </div>
                                            </div>
                                        </div>

                                    </label>
                                </div>
                            </template>
                            <template #item-is_end="{ id, is_end }">
                                <div class="flex items-center cursor-pointer justify-center">
                                    <input type="checkbox" :id="'is_end-' + id" class="hidden"
                                        @change="handleCompledChange(id, is_end)" />
                                    <label :for="'is_end-' + id" class="flex items-center cursor-pointer">
                                        <div class="relative">
                                            <div class="toggle-path bg-gray-300 w-9 h-5 rounded-full p-0">
                                                <div class="toggle-circle  w-5 h-5 rounded-full shadow-md"
                                                    :class="{ 'transform translate-x-full bg-purple-500': is_end == 1, 'bg-white': is_end == 0 }">
                                                </div>
                                            </div>
                                        </div>

                                    </label>
                                </div>
                            </template>

                            <template #item-highlight="{ id, highlight }">
                                <div class="flex items-center cursor-pointer justify-center">
                                    <input type="checkbox" :id="'highlightCheckbox-' + id" class="hidden"
                                        @change="handleHighlightChange(id, highlight)" />
                                    <label :for="'highlightCheckbox-' + id" class="flex items-center cursor-pointer">
                                        <div class="relative">
                                            <div class="toggle-path bg-gray-300 w-9 h-5 rounded-full p-0">
                                                <div class="toggle-circle  w-5 h-5 rounded-full shadow-md"
                                                    :class="{ 'transform translate-x-full bg-purple-500': highlight == 1, 'bg-white': highlight == 0 }">
                                                </div>
                                            </div>
                                        </div>

                                    </label>
                                </div>
                            </template>

                            <template #item-ord="{ id, name, ord }">
                                <div class="py-3 flex items-center justify-center">
                                    <input type="number" class=" text-black rounded  text-center px-1" :value="ord"
                                        style="max-width: 50px;" @input="changeORD(id, name, $event)">
                                </div>
                            </template>

                            <template #item-operation="{ id, name }">
                                <div class="py-3 flex items-center justify-center">
                                    <button class="bg-red-600 text-white px-2 py-1 rounded-md mr-5"
                                        @click="showModalDeleteItem(id, name)">
                                        <icon :icon="['fas', 'x']" />
                                    </button>
                                    <a target="_blank" :href="route('Product.Edit', id)"
                                        class="bg-yellow-600 text-white px-2 py-1 rounded-md mr-5">
                                    <icon :icon="['fas', 'pen-to-square']" />
                                    </a>
                                </div>
                            </template>


                            <template #item-episode="{ newEpisode, id }">
                                <div class="flex items-center justify-center">
                                    <!-- <div class="flex items-center jsutiy-center gap-3">
                                        ....
                                        <span v-for="(item, index) in newEpisode.reverse()" :key="index">
                                            {{ item.name }}
                                        </span>
                                    </div> -->

                                    <div class="ms-5">
                                        <Link :href="route('Product.Episode', id)">
                                        <button class="py-1 px-2 rounded bg-gray-600 text-white hover:text-white/80">
                                            <icon icon="clipboard-list" class="h-5" />
                                        </button>
                                        </Link>

                                    </div>
                                </div>
                            </template>

                        </DataTable>



                    </div>
                </div>
            </div>
        </div>
        <DialogModal :show="modalDelete" @close="isModal = !isModal">

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
                    @click="isModal = !isModal">
                    Hủy lệnh
                </button>
            </template>
        </DialogModal>

    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch, nextTick } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';

const isTableLoading = ref(false);
const isModal = ref(false);
const items = ref([]);
const reRender = ref(1);
const serverItemsLength = ref(0);

// Các tùy chọn server-side
const serverOptions = ref({
    page: 1,
    rowsPerPage: 10,
    sortBy: 'created_at',
    sortType: 'desc',
    name: '',
});

// URL API từ server dựa trên các tùy chọn
const restApiUrl = computed(() => {
    const { page, rowsPerPage, sortBy, sortType, name } = serverOptions.value;
    let url = `/products/load-data-table?page=${page}&per_page=${rowsPerPage}&sortBy=${sortBy}&sortType=${sortType}`;
    if (name) url += `&name=${name}`;
    return url;
});

// Hàm tải dữ liệu từ server
const loadFromServer = async () => {

    isTableLoading.value = true;
    reRender.value++;

    try {
        const response = await axios.get(restApiUrl.value);
        await nextTick(); // Đảm bảo render trước khi cập nhật
        const data = await response.data
        items.value = data.data;
        serverItemsLength.value = response.data.total;
    } catch (error) {
        console.error(error);
    } finally {
        isTableLoading.value = false;
    }
};
// // Watcher cho từng thuộc tính để hạn chế vòng lặp
// watch(() => serverOptions.value.page, loadFromServer);
// watch(() => serverOptions.value.rowsPerPage, loadFromServer);
// watch(() => serverOptions.value.sortBy, loadFromServer);
// watch(() => serverOptions.value.sortType, loadFromServer);
// watch(() => serverOptions.value.name, debounce(loadFromServer, 300)); // Giới hạn tần suất gọi


// Khởi động khi component được mount
onMounted(() => {
    loadFromServer();
});
watch(
    serverOptions,
    (value) => {
        loadFromServer();
    },
    { deep: true }
);
// Các biến liên quan đến tìm kiếm và chọn item

const checkboxDeleteToTrash = ref(false);
const itemsDelete = ref([]);
const modalDelete = ref(false);
const itemsSelected = ref([]);
const headers = [
    { text: 'Tên dữ liệu', value: 'name' },
    { text: 'Hoàn Thành', value: 'is_end' },
    { text: 'Nổi bật', value: 'highlight' },
    { text: 'Ẩn Hiện', value: 'status' },
    { text: 'Hành động', value: 'operation' },
    { text: 'Tập truyện', value: 'episode' },
];

// Hàm xử lý xóa nhiều item
const deleteItems = async () => {
    const dataDelete = itemsDelete.value.map(item => item.id);

    try {
        await axios.post('/delete-items', {
            tb: 'products',
            dataId: dataDelete,
            trash: false,
        });
        loadFromServer();
        modalDelete.value = false;
        toast.success('Xóa dữ liệu thành công', { autoClose: 1500 });
    } catch (error) {
        console.error('Error while deleting items:', error);
    }
};

// Hiển thị modal xóa nhiều item
const showModalDeleteMutipleItem = () => {
    itemsDelete.value = itemsSelected.value.map(item => ({
        id: item.id,
        name: item.name,
    }));
    modalDelete.value = true;
};

// Hiển thị modal xóa item đơn lẻ
const showModalDeleteItem = (deleteId, deleteName) => {
    itemsDelete.value = [{ id: deleteId, name: deleteName }];
    modalDelete.value = true;
};

// Hàm thay đổi trạng thái (Ẩn/Hiện)
const handleStatusChange = async (id, currentStatus) => {
    if (isTableLoading.value) return; // Tránh gọi trùng lặp
    isTableLoading.value = true;

    try {
        const newStatus = currentStatus === 1 ? 0 : 1;
        await axios.post('/change-status', {
            tb: 'products',
            id: id,
            status: newStatus,
        });

        toast.success(
            newStatus === 1 ? 'Hiện dữ liệu thành công' : 'Ẩn dữ liệu thành công',
            { autoClose: 1000 }
        );
        loadFromServer();
    } catch (error) {
        console.error('Error while changing status:', error);
    } finally {
        isTableLoading.value = false;
    }
};

// Hàm thay đổi nổi bật (highlight)
const handleHighlightChange = async (id, highlight) => {
    if (isTableLoading.value) return; // Tránh gọi trùng lặp
    isTableLoading.value = true;

    try {
        const newHighlight = highlight === 1 ? 0 : 1;
        await axios.post('/change-highlight', {
            tb: 'products',
            id: id,
            highlight: newHighlight,
        });

        toast.success('Chỉnh sửa highlight thành công!', { autoClose: 1000 });
        loadFromServer();
    } catch (error) {
        console.error('Error while changing highlight:', error);
    } finally {
        isTableLoading.value = false;
    }
};
const handleCompledChange = async (id, is_end) => {
    if (isTableLoading.value) return; // Tránh gọi trùng lặp
    isTableLoading.value = true;
    const value = is_end === 1 ? 0 : 1;

    axios.post(route('Product.ChangeCompleted'), {
        id: id,
        is_end: value,
    })
        .then((response) => {
            loadFromServer();

            toast.success(response.data.message, {
                autoClose: 2000,
            });
        })
        .catch((error) => {
            toast.error(error.response.data.message, {
                autoClose: 1500,
            });
        });
    isTableLoading.value = false;


};



</script>
