<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { echo } from '@laravel/echo-vue';
import { ArrowLeft } from '@lucide/vue';
import { Board, Card, CardStatus } from '@/types';
import { useBoardStore } from '@/Stores/board';
import { usePresenceStore } from '@/Stores/presence';
import { useActivityStore } from '@/Stores/activity';
import { Card as UICard, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';
import CardModal from '@/components/CardModal.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { show as showWorkspace } from '@/routes/workspaces';
import { store as storeBoardCard } from '@/routes/boards/cards';

const props = defineProps<{
    board: Board;
    workspace: any;
}>();

const boardStore = useBoardStore();
const presenceStore = usePresenceStore();
const activityStore = useActivityStore();

const selectedCard = ref<Card | null>(null);
const isModalOpen = ref(false);
const draggingCardId = ref<number | null>(null);
const hoveredStatus = ref<CardStatus | null>(null);

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
    newCardForm.post(storeBoardCard(props.board.id), {
        preserveScroll: true,
        onSuccess: () => newCardForm.reset(),
    });
};

const cardCountByStatus = computed<Record<CardStatus, number>>(() => ({
    [CardStatus.Todo]: boardStore.todoCards.length,
    [CardStatus.InProgress]: boardStore.inProgressCards.length,
    [CardStatus.Done]: boardStore.doneCards.length,
}));

const moveCardToStatus = (cardId: number, status: CardStatus) => {
    const card = boardStore.cards.find((currentCard) => currentCard.id === cardId);

    if (!card || card.status === status) {
        return;
    }

    boardStore.moveCard(cardId, status, cardCountByStatus.value[status]);
};

const onDragStart = (cardId: number) => {
    draggingCardId.value = cardId;
};

const onDragEnterLane = (status: CardStatus) => {
    if (draggingCardId.value === null) {
        return;
    }

    hoveredStatus.value = status;
};

const onDragLeaveLane = (event: DragEvent, status: CardStatus) => {
    if (hoveredStatus.value !== status) {
        return;
    }

    const relatedTarget = event.relatedTarget;

    if (relatedTarget instanceof Node && event.currentTarget instanceof Node && event.currentTarget.contains(relatedTarget)) {
        return;
    }

    hoveredStatus.value = null;
};

const onDrop = (status: CardStatus) => {
    if (draggingCardId.value === null) {
        return;
    }

    moveCardToStatus(draggingCardId.value, status);
    draggingCardId.value = null;
    hoveredStatus.value = null;
};

const onDragEnd = () => {
    draggingCardId.value = null;
    hoveredStatus.value = null;
};

onMounted(() => {
    boardStore.setBoard(props.board);
    activityStore.setActivities(props.board.activities || []);

    const echoInstance = echo();

    // Echo listeners
    echoInstance.join(`board.${props.board.id}`)
        .here((users: any) => presenceStore.setUsers(users))
        .joining((user: any) => presenceStore.addUser(user))
        .leaving((user: any) => presenceStore.removeUser(user))
        .error((error: any) => {
            console.error('Failed to join board presence channel', error);
        })
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
        .listen('.comment.created', (e: any) => {
            const card = boardStore.cards.find(c => c.id === e.comment.card_id);
            if (card) {
                card.comments = card.comments ?? [];

                if (!card.comments.find((comment) => comment.id === e.comment.id)) {
                    card.comments.unshift(e.comment);
                }
            }
        });
});

onUnmounted(() => {
    echo().leave(`board.${props.board.id}`);
});
</script>

<template>
    <Head :title="board.name" />

    <div class="mx-auto flex w-full max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex-1">
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Button as-child variant="outline" size="sm">
                        <Link :href="showWorkspace(workspace.id)">
                            <ArrowLeft class="mr-2 size-4" />
                            Back
                        </Link>
                    </Button>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                        {{ board.name }}
                    </h2>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Currently Viewing:</span>
                    <div class="flex -space-x-2">
                        <Avatar
                            v-for="user in presenceStore.users"
                            :key="user.id"
                            class="h-8 w-8 border-2 border-white dark:border-gray-900"
                        >
                            <AvatarFallback>{{ user.name?.charAt(0) ?? 'U' }}</AvatarFallback>
                        </Avatar>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <!-- Todo -->
                <div class="flex min-h-64 flex-col gap-4 rounded-lg border border-dashed p-2 transition-all"
                     :class="hoveredStatus === CardStatus.Todo
                        ? 'border-blue-400 bg-blue-50/60 ring-2 ring-blue-300/60 dark:border-blue-500 dark:bg-blue-950/30 dark:ring-blue-500/50'
                        : 'border-transparent'"
                     @dragenter.prevent="onDragEnterLane(CardStatus.Todo)"
                     @dragleave="onDragLeaveLane($event, CardStatus.Todo)"
                     @dragover.prevent
                     @drop.prevent="onDrop(CardStatus.Todo)">
                    <h3 class="flex items-center justify-between border-b pb-2 text-lg font-bold">
                        Todo
                        <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-500">{{ boardStore.todoCards.length }}</span>
                    </h3>
                    <div v-for="card in boardStore.todoCards" :key="card.id" @click="openCard(card)" class="cursor-pointer rounded-lg transition-all hover:ring-2 hover:ring-blue-400"
                         draggable="true"
                         @dragstart="onDragStart(card.id)"
                         @dragend="onDragEnd">
                        <UICard shadow="sm">
                            <CardHeader class="p-4">
                                <CardTitle class="text-md">{{ card.title }}</CardTitle>
                            </CardHeader>
                            <CardContent v-if="card.description" class="p-4 pt-0">
                                <p class="line-clamp-2 text-sm text-gray-600">{{ card.description }}</p>
                            </CardContent>
                        </UICard>
                    </div>
                    <div
                        v-if="hoveredStatus === CardStatus.Todo"
                        class="rounded-md border-2 border-dashed border-blue-400 bg-blue-100/60 px-3 py-2 text-xs font-medium text-blue-700 transition-all dark:border-blue-500 dark:bg-blue-900/40 dark:text-blue-200"
                    >
                        Drop card in Todo
                    </div>
                    <div class="mt-2">
                        <Input v-model="newCardForm.title" placeholder="+ Add Card" @keyup.enter="createCard(CardStatus.Todo)" class="bg-white" />
                    </div>
                </div>

                <!-- In Progress -->
                <div class="flex min-h-64 flex-col gap-4 rounded-lg border border-dashed p-2 transition-all"
                     :class="hoveredStatus === CardStatus.InProgress
                        ? 'border-blue-400 bg-blue-50/60 ring-2 ring-blue-300/60 dark:border-blue-500 dark:bg-blue-950/30 dark:ring-blue-500/50'
                        : 'border-transparent'"
                     @dragenter.prevent="onDragEnterLane(CardStatus.InProgress)"
                     @dragleave="onDragLeaveLane($event, CardStatus.InProgress)"
                     @dragover.prevent
                     @drop.prevent="onDrop(CardStatus.InProgress)">
                    <h3 class="flex items-center justify-between border-b pb-2 text-lg font-bold">
                        In Progress
                        <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-500">{{ boardStore.inProgressCards.length }}</span>
                    </h3>
                    <div v-for="card in boardStore.inProgressCards" :key="card.id" @click="openCard(card)" class="cursor-pointer rounded-lg transition-all hover:ring-2 hover:ring-blue-400"
                         draggable="true"
                         @dragstart="onDragStart(card.id)"
                         @dragend="onDragEnd">
                        <UICard>
                            <CardHeader class="p-4">
                                <CardTitle class="text-md">{{ card.title }}</CardTitle>
                            </CardHeader>
                        </UICard>
                    </div>
                    <div
                        v-if="hoveredStatus === CardStatus.InProgress"
                        class="rounded-md border-2 border-dashed border-blue-400 bg-blue-100/60 px-3 py-2 text-xs font-medium text-blue-700 transition-all dark:border-blue-500 dark:bg-blue-900/40 dark:text-blue-200"
                    >
                        Drop card in In Progress
                    </div>
                </div>

                <!-- Done -->
                <div class="flex min-h-64 flex-col gap-4 rounded-lg border border-dashed p-2 transition-all"
                     :class="hoveredStatus === CardStatus.Done
                        ? 'border-blue-400 bg-blue-50/60 ring-2 ring-blue-300/60 dark:border-blue-500 dark:bg-blue-950/30 dark:ring-blue-500/50'
                        : 'border-transparent'"
                     @dragenter.prevent="onDragEnterLane(CardStatus.Done)"
                     @dragleave="onDragLeaveLane($event, CardStatus.Done)"
                     @dragover.prevent
                     @drop.prevent="onDrop(CardStatus.Done)">
                    <h3 class="flex items-center justify-between border-b pb-2 text-lg font-bold">
                        Done
                        <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-500">{{ boardStore.doneCards.length }}</span>
                    </h3>
                    <div v-for="card in boardStore.doneCards" :key="card.id" @click="openCard(card)" class="cursor-pointer rounded-lg transition-all hover:ring-2 hover:ring-blue-400"
                         draggable="true"
                         @dragstart="onDragStart(card.id)"
                         @dragend="onDragEnd">
                        <UICard>
                            <CardHeader class="p-4">
                                <CardTitle class="text-md">{{ card.title }}</CardTitle>
                            </CardHeader>
                        </UICard>
                    </div>
                    <div
                        v-if="hoveredStatus === CardStatus.Done"
                        class="rounded-md border-2 border-dashed border-blue-400 bg-blue-100/60 px-3 py-2 text-xs font-medium text-blue-700 transition-all dark:border-blue-500 dark:bg-blue-900/40 dark:text-blue-200"
                    >
                        Drop card in Done
                    </div>
                </div>
            </div>

            <CardModal v-model:open="isModalOpen" :card="selectedCard" />
        </div>

        <!-- Activity Feed -->
        <div class="hidden w-80 lg:block">
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
                                        <span class="font-medium text-gray-500">"{{ activity.payload?.card_title }}"</span>
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
</template>
