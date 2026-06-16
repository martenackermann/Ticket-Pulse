<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Board, Card, CardStatus, Activity } from '@/types';
import { useBoardStore } from '@/Stores/board';
import { usePresenceStore } from '@/Stores/presence';
import { useActivityStore } from '@/Stores/activity';
import { Button } from '@/Components/ui/button';
import { Card as UICard, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { ScrollArea } from '@/Components/ui/scroll-area';
import { Separator } from '@/Components/ui/separator';
import CardModal from '@/Components/CardModal.vue';
import { Input } from '@/Components/ui/input';

const props = defineProps<{
    board: Board;
    workspace: any;
}>();

const boardStore = useBoardStore();
const presenceStore = usePresenceStore();
const activityStore = useActivityStore();

const selectedCard = ref<Card | null>(null);
const isModalOpen = ref(false);

const openCard = (card: Card) => {
    selectedCard.value = card;
    isModalOpen.value = true;
};

const newCardForm = useForm({
    title: '',
    status: CardStatus.Todo
});

const createCard = (status: CardStatus) => {
    newCardForm.status = status;
    newCardForm.post(route('boards.cards.store', props.board.id), {
        preserveScroll: true,
        onSuccess: () => newCardForm.reset()
    });
};

onMounted(() => {
    boardStore.setBoard(props.board);
    activityStore.setActivities(props.board.activities || []);

    // Echo listeners
    (window as any).Echo.join(`board.${props.board.id}`)
        .here((users: any) => presenceStore.setUsers(users))
        .joining((user: any) => presenceStore.addUser(user))
        .leaving((user: any) => presenceStore.removeUser(user))
        .listen('CardCreated', (e: any) => {
            boardStore.addCard(e.card);
        })
        .listen('CardMoved', (e: any) => {
            boardStore.updateCard(e.card);
        })
        .listen('CardUpdated', (e: any) => {
            boardStore.updateCard(e.card);
        })
        .listen('CardDeleted', (e: any) => {
            boardStore.removeCard(e.cardId);
        })
        .listen('CommentCreated', (e: any) => {
            // Update card with new comment
            const card = boardStore.cards.find(c => c.id === e.comment.card_id);
            if (card) {
                if (!card.comments) card.comments = [];
                card.comments.unshift(e.comment);
            }
        });
});

onUnmounted(() => {
    (window as any).Echo.leave(`board.${props.board.id}`);
});
</script>

<template>
    <Head :title="board.name" />

    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ board.name }}
                </h2>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Currently Viewing:</span>
                    <div class="flex -space-x-2">
                        <Avatar v-for="user in presenceStore.users" :key="user.id" class="border-2 border-white w-8 h-8">
                            <AvatarFallback>{{ user.name.charAt(0) }}</AvatarFallback>
                        </Avatar>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex gap-6">
                <!-- Kanban Board -->
                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Todo -->
                    <div class="flex flex-col gap-4">
                        <h3 class="font-bold text-lg border-b pb-2 flex justify-between items-center">
                            Todo
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-500">{{ boardStore.todoCards.length }}</span>
                        </h3>
                        <div v-for="card in boardStore.todoCards" :key="card.id" @click="openCard(card)" class="cursor-pointer hover:ring-2 hover:ring-blue-400 rounded-lg transition-all">
                            <UICard shadow="sm">
                                <CardHeader class="p-4">
                                    <CardTitle class="text-md">{{ card.title }}</CardTitle>
                                </CardHeader>
                                <CardContent v-if="card.description" class="p-4 pt-0">
                                    <p class="text-sm text-gray-600 line-clamp-2">{{ card.description }}</p>
                                </CardContent>
                            </UICard>
                        </div>
                        <div class="mt-2">
                            <Input v-model="newCardForm.title" placeholder="+ Add Card" @keyup.enter="createCard(CardStatus.Todo)" class="bg-white" />
                        </div>
                    </div>

                    <!-- In Progress -->
                    <div class="flex flex-col gap-4">
                        <h3 class="font-bold text-lg border-b pb-2 flex justify-between items-center">
                            In Progress
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-500">{{ boardStore.inProgressCards.length }}</span>
                        </h3>
                        <div v-for="card in boardStore.inProgressCards" :key="card.id" @click="openCard(card)" class="cursor-pointer hover:ring-2 hover:ring-blue-400 rounded-lg transition-all">
                            <UICard>
                                <CardHeader class="p-4">
                                    <CardTitle class="text-md">{{ card.title }}</CardTitle>
                                </CardHeader>
                            </UICard>
                        </div>
                    </div>

                    <!-- Done -->
                    <div class="flex flex-col gap-4">
                        <h3 class="font-bold text-lg border-b pb-2 flex justify-between items-center">
                            Done
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-500">{{ boardStore.doneCards.length }}</span>
                        </h3>
                        <div v-for="card in boardStore.doneCards" :key="card.id" @click="openCard(card)" class="cursor-pointer hover:ring-2 hover:ring-blue-400 rounded-lg transition-all">
                            <UICard>
                                <CardHeader class="p-4">
                                    <CardTitle class="text-md">{{ card.title }}</CardTitle>
                                </CardHeader>
                            </UICard>
                        </div>
                    </div>
                </div>

                <CardModal v-model:open="isModalOpen" :card="selectedCard" />

                <!-- Activity Feed -->
                <div class="w-80 hidden lg:block">
                    <UICard class="h-full">
                        <CardHeader>
                            <CardTitle>Recent Activity</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <ScrollArea class="h-[600px] pr-4">
                                <div v-for="activity in activityStore.activities" :key="activity.id" class="mb-4">
                                    <div class="flex gap-2">
                                        <div class="flex-1">
                                            <p class="text-sm">
                                                <span class="font-bold">{{ activity.user?.name }}</span>
                                                {{ activity.event_type.replace('_', ' ') }}
                                                <span class="text-gray-500 font-medium">"{{ activity.payload?.card_title }}"</span>
                                            </p>
                                            <p class="text-xs text-gray-400">{{ new Date(activity.created_at).toLocaleString() }}</p>
                                        </div>
                                    </div>
                                    <Separator class="my-2" />
                                </div>
                            </ScrollArea>
                        </CardContent>
                    </UICard>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
