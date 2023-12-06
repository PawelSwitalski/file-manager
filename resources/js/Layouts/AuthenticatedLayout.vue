<template>
    <div class="h-screen bg-gray-50 flex w-full gap-4">
        <Navigation />

        <main @drop.prevent="handleDrop"
              @dragover.prevent="onDragOver"
              @dragleave.prevent="onDragLeave"
              class="flex flex-col flex-1 px-4 overflow-hidden"
              :class="dragOver ? 'dropzone' : ''">

            <template v-if="dragOver" class="text-gray-500 text-center py-8 text-sm">
                Drop files here to upload
            </template>
            <template v-else>
                <div class="flex items-center justify-between w-full">
                    <SearchForm />
                    <UserSettingsDropdown />
                </div>
                <div class="flex-1 flex flex-col overflow-hidden">
                    <slot />
                </div>
            </template>
        </main>
    </div>

    <ErrorDialog/>
    <FormProgress :form="fileUploadForm" />
</template>


<script setup>
import {onMounted, ref} from 'vue';
import Navigation from "@/Components/app/Navigation.vue";
import SearchForm from "@/Components/app/SearchForm.vue";
import UserSettingsDropdown from "@/Components/app/UserSettingsDropdown.vue";
import {emitter, FILE_UPLOAD_STARTED, showErrorDialog} from "@/event-bus.js";
import {useForm, usePage} from "@inertiajs/vue3";
import FormProgress from "@/Components/app/FormProgress.vue";
import ErrorDialog from "@/Components/app/ErrorDialog.vue";

// Uses
const page = usePage();
const fileUploadForm = useForm({
    files: [],
    relative_paths: [],
    parent_id: null,
});

// Refs
const dragOver = ref(false);

// Methods
function handleDrop(ev){
    dragOver.value = false;
    const files = ev.dataTransfer.files;

    if (!files.length) {
        return;
    }

    if (files.length > 0) {
        emitter.emit(FILE_UPLOAD_STARTED, files);
    }
}
function onDragOver(){
    dragOver.value = true;
}
function onDragLeave(){
    dragOver.value = false;
}

function uploadFiles(files) {
    console.log(files);

    fileUploadForm.parent_id = page.props.folder.id;
    fileUploadForm.files = files;
    fileUploadForm.relative_paths = [...files].map(f => f.webkitRelativePath);

    fileUploadForm.post(route('file.store'), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('success');
        },
        onError: errors => {
            console.log('error');
            let message = '';

            if (Object.keys(errors).length > 0) {
                message = errors[Object.keys(errors)[0]];
            } else {
                message = 'Something went wrong';
            }

            showErrorDialog(message);
        },
        onFinish: () => {
            console.log('finish');
            fileUploadForm.clearErrors()
            fileUploadForm.reset()
        },
    });
}

// Hooks
onMounted(() => {
    emitter.on(FILE_UPLOAD_STARTED, uploadFiles);
});

</script>

<style scoped>
.dropzone {
    width: 100%;
    height: 100%;
    color: #8d8d8d;
    border: 2px dashed rgb(128, 128, 128);
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>
