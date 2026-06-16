<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { dashboard } from '@/routes';
import {
    index as workspacesIndex,
    show as showWorkspace,
    store as storeWorkspace,
} from '@/routes/workspaces';
import { Workspace } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Workspaces',
                href: workspacesIndex(),
            },
        ],
    },
});

defineProps<{
    workspaces: Workspace[];
}>();

const form = useForm({
    name: '',
});

const createWorkspace = () => {
    form.post(storeWorkspace(), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Workspaces" />

    <div class="mx-auto w-full max-w-7xl space-y-6">
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

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <Link
                v-for="workspace in workspaces"
                :key="workspace.id"
                :href="showWorkspace(workspace.id)"
            >
                <Card class="cursor-pointer transition-colors hover:border-blue-500">
                    <CardHeader>
                        <CardTitle>{{ workspace.name }}</CardTitle>
                    </CardHeader>
                </Card>
            </Link>
        </div>
    </div>
</template>
