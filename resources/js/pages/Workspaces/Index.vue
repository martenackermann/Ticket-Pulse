<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Workspace } from '@/types';
import { Button } from '@/Components/ui/button';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';

defineProps<{
    workspaces: Workspace[];
}>();

const form = useForm({
    name: '',
});

const createWorkspace = () => {
    form.post(route('workspaces.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Workspaces" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Workspaces
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Create Workspace</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="createWorkspace" class="flex gap-4">
                            <Input v-model="form.name" placeholder="Workspace Name" required />
                            <Button :disabled="form.processing">Create</Button>
                        </form>
                    </CardContent>
                </Card>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Link v-for="workspace in workspaces" :key="workspace.id" :href="route('workspaces.show', workspace.id)">
                        <Card class="hover:border-blue-500 transition-colors cursor-pointer">
                            <CardHeader>
                                <CardTitle>{{ workspace.name }}</CardTitle>
                            </CardHeader>
                        </Card>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
