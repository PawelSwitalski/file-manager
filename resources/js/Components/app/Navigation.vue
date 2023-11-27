<template>
    <nav class="min-w-[200px]">
        <Link class="h-[80px] px-3 flex items-center gap-3" :href="route('files.index')">
            <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
            LaraStorage
        </Link>

        <div class="px-3">
            <CreateNewDropdown />

            <div class="py-3">
                <NavLink :href="route('files.index')" :active="$page.url === '/files'">My Files</NavLink>
                <NavLink href="/">Shared with me</NavLink>
                <NavLink href="/">Shared by me</NavLink>
                <NavLink href="/">Trash</NavLink>
            </div>
        </div>
    </nav>
</template>

<script setup>

import { Link } from "@inertiajs/vue3";

import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import CreateNewDropdown from "@/Components/app/CreateNewDropdown.vue";
import NavLink from "@/Components/NavLink.vue";
</script>

<style scoped>

</style>
