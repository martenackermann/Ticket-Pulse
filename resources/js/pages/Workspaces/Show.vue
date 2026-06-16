<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { dashboard } from '@/routes';
import { show as showBoard } from '@/routes/boards';
import { store as storeBoard } from '@/routes/workspaces/boards';
import { index as workspacesIndex } from '@/routes/workspaces';
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

const props = defineProps<{
    workspace: Workspace;
}>();

const form = useForm({
    name: '',
});

const createBoard = () => {
    form.post(storeBoard(props.workspace.id), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head :title="workspace.name" />

    <div class="mx-auto w-full max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4">
            <Button as-child variant="outline" size="sm">
                <Link :href="workspacesIndex()">
                    <ArrowLeft class="mr-2 size-4" />
                    Back to workspaces
                </Link>
            </Button>

            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ workspace.name }}
            </h1>
        </div>

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

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <Link
                v-for="board in workspace.boards"
                :key="board.id"
                :href="showBoard(board.id)"
            >
                <Card class="cursor-pointer transition-colors hover:border-blue-500">
                    <CardHeader>
                        <CardTitle>{{ board.name }}</CardTitle>
                    </CardHeader>
                </Card>
            </Link>
        </div>
    </div>
</template>
