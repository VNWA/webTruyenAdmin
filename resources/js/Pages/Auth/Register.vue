<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';

</script>

<template>

    <Head title="Register" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="mb-4 text-2xl font-bold text-black  text-center">
            Vinawebapp.com - Thiết kế website chuyên nghiệp
        </div>
        <div class="mb-4 text-lg text-center text-purple-500 hover:text-xl cursor-pointer">
            <a href="http://vinawebapp.com">Vinawebapp.com</a>
        </div>



    </AuthenticationCard>
</template>
