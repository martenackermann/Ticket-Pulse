<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Workspace } from '@/types';
import { Button } from '@/Components/ui/button';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';

const props = defineProps<{
    workspace: Workspace;
}>();

const form = useForm({
    name: '',
});

const createBoard = () => {
    form.post(route('workspaces.boards.store', props.workspace.id), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head :title="workspace.name" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ workspace.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Create Board</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="createBoard" class="flex gap-4">
                            <Input v-model="form.name" placeholder="Board Name" required />
                            <Button :disabled="form.processing">Create Board</Button>
                        </form>
                    </CardContent>
                </Card>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Link v-for="board in workspace.boards" :key="board.id" :href="route('boards.show', board.id)">
                        <Card class="hover:border-blue-500 transition-colors cursor-pointer">
                            <CardHeader>
                                <CardTitle>{{ board.name }}</CardTitle>
                            </CardHeader>
                        </Card>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
