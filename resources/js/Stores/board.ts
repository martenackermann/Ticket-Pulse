import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { Card, Board, CardStatus } from '@/types';
import { router } from '@inertiajs/vue3';
import { move as moveCardRoute } from '@/routes/cards';

export const useBoardStore = defineStore('board', () => {
    const board = ref<Board | null>(null);
    const cards = ref<Card[]>([]);

    const todoCards = computed(() => cards.value.filter(c => c.status === CardStatus.Todo).sort((a, b) => a.position - b.position));
    const inProgressCards = computed(() => cards.value.filter(c => c.status === CardStatus.InProgress).sort((a, b) => a.position - b.position));
    const doneCards = computed(() => cards.value.filter(c => c.status === CardStatus.Done).sort((a, b) => a.position - b.position));

    function setBoard(newBoard: Board) {
        board.value = newBoard;
        cards.value = newBoard.cards || [];
    }

    function moveCard(cardId: number, newStatus: CardStatus, newPosition: number) {
        const card = cards.value.find(c => c.id === cardId);
        if (!card) {
            return;
        }

        card.status = newStatus;
        card.position = newPosition;

        router.post(moveCardRoute(cardId), {
            status: newStatus,
            position: newPosition
        }, {
            preserveScroll: true,
            preserveState: true,
        });
    }

    function addCard(card: Card) {
        if (!cards.value.find(c => c.id === card.id)) {
            cards.value.push(card);
        }
    }

    function updateCard(card: Card) {
        const index = cards.value.findIndex(c => c.id === card.id);
        if (index !== -1) {
            cards.value[index] = card;
        }
    }

    function removeCard(cardId: number) {
        cards.value = cards.value.filter(c => c.id !== cardId);
    }

    return {
        board,
        cards,
        todoCards,
        inProgressCards,
        doneCards,
        setBoard,
        moveCard,
        addCard,
        updateCard,
        removeCard
    };
});
