<template>
    <Menu as="div" class="relative block text-left">

        <MenuButton
            class="inline-flex w-full justify-center rounded-md px-4 py-2 text-sm font-medium text-gray-800 shadow-sm ring-1 ring-gray-300 hover:bg-black/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75"
        >
            Create New
        </MenuButton>


        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <MenuItems
                class="absolute left-0 mt-2 w-32 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
            >
                <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                        <a href="#" @click.prevent="showCreateFolderModal" class="text-gray-700 block px-4 py-2 text-sm">
                            New Folder
                        </a>
                    </MenuItem>
                </div>
                <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                        <a class="text-gray-700 block px-4 py-2 text-sm">
                            Upload Folder
                        </a>
                    </MenuItem>
                </div>
                <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                        <a class="text-gray-700 block px-4 py-2 text-sm">
                            Upload Files
                        </a>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
    <CreateFolderModal v-model="createFolderModal"/>
</template>

<script setup>
// Imports
import {Menu, MenuButton, MenuItem, MenuItems} from "@headlessui/vue";
import CreateFolderModal from "@/Components/app/CreateFolderModal.vue";
import {ref} from "vue";

//Refs
const createFolderModal = ref(false);

// Methods
function showCreateFolderModal() {
    createFolderModal.value = true;
}
</script>

<style scoped>

</style>
