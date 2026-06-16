<script setup lang="ts">
import { ref, watch } from 'vue';
import { Card, Comment } from '@/types';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/Components/ui/dialog';
import { Button } from '@/Components/ui/button';
import { Textarea } from '@/Components/ui/textarea';
import { Input } from '@/Components/ui/input';
import { ScrollArea } from '@/Components/ui/scroll-area';
import { Separator } from '@/Components/ui/separator';
import { useBoardStore } from '@/Stores/board';
import axios from 'axios';

const props = defineProps<{
    card: Card | null;
    open: boolean;
}>();

const emit = defineEmits(['update:open']);

const boardStore = useBoardStore();
const newComment = ref('');
const isGeneratingDescription = ref(false);
const isGeneratingBreakdown = ref(false);
const isSummarizing = ref(false);
const aiSummary = ref('');
const aiTasks = ref<string[]>([]);

const generateDescription = async () => {
    if (!props.card) return;
    isGeneratingDescription.value = true;
    try {
        const response = await axios.post(route('ai.generate-description'), { title: props.card.title });
        props.card.description = response.data.description;
        // In a real app, we'd save this to the server too
    } finally {
        isGeneratingDescription.value = false;
    }
};

const generateBreakdown = async () => {
    if (!props.card) return;
    isGeneratingBreakdown.value = true;
    try {
        const response = await axios.post(route('ai.generate-breakdown'), { title: props.card.title });
        aiTasks.value = response.data.tasks;
    } finally {
        isGeneratingBreakdown.value = false;
    }
};

const summarizeComments = async () => {
    if (!props.card) return;
    isSummarizing.value = true;
    try {
        const response = await axios.post(route('ai.summarize-comments', props.card.id));
        aiSummary.value = response.data.summary;
    } finally {
        isSummarizing.value = false;
    }
};

const addComment = async () => {
    if (!props.card || !newComment.value) return;
    await axios.post(route('cards.comments.store', props.card.id), { body: newComment.value });
    newComment.value = '';
    // Comment will be added via Echo listener or Inertia reload
};

watch(() => props.open, (isOpen) => {
    if (!isOpen) {
        aiSummary.value = '';
        aiTasks.value = [];
    }
});
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-[600px] max-h-[90vh] overflow-y-auto">
            <DialogHeader v-if="card">
                <DialogTitle>{{ card.title }}</DialogTitle>
                <p class="text-sm text-gray-500">In list {{ card.status }}</p>
            </DialogHeader>

            <div v-if="card" class="grid gap-6 py-4">
                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-bold uppercase text-gray-500">Description</label>
                        <Button variant="ghost" size="sm" @click="generateDescription" :disabled="isGeneratingDescription">
                            {{ isGeneratingDescription ? 'Generating...' : '✨ Generate with AI' }}
                        </Button>
                    </div>
                    <Textarea v-model="card.description" placeholder="Add a more detailed description..." />
                </div>

                <div v-if="aiTasks.length > 0" class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="text-sm font-bold text-blue-800 mb-2">AI Task Breakdown</h4>
                    <ul class="list-disc list-inside text-sm text-blue-700">
                        <li v-for="task in aiTasks" :key="task">{{ task }}</li>
                    </ul>
                </div>
                <Button v-else variant="outline" size="sm" @click="generateBreakdown" :disabled="isGeneratingBreakdown">
                    📋 AI Task Breakdown
                </Button>

                <Separator />

                <div class="grid gap-4">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-bold uppercase text-gray-500">Comments</label>
                        <Button v-if="card.comments && card.comments.length > 3" variant="ghost" size="sm" @click="summarizeComments" :disabled="isSummarizing">
                            {{ isSummarizing ? 'Summarizing...' : '📝 Summarize Discussion' }}
                        </Button>
                    </div>
                    
                    <div v-if="aiSummary" class="bg-green-50 p-3 rounded-lg text-sm text-green-800 border border-green-200">
                        <strong>AI Summary:</strong> {{ aiSummary }}
                    </div>

                    <div class="flex gap-2">
                        <Input v-model="newComment" placeholder="Write a comment..." @keyup.enter="addComment" />
                        <Button @click="addComment">Send</Button>
                    </div>

                    <ScrollArea class="h-[200px]">
                        <div v-for="comment in card.comments" :key="comment.id" class="mb-4">
                            <div class="flex gap-2">
                                <Avatar class="w-6 h-6">
                                    <AvatarFallback>{{ comment.user?.name.charAt(0) }}</AvatarFallback>
                                </Avatar>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold">{{ comment.user?.name }}</span>
                                        <span class="text-xs text-gray-400">{{ new Date(comment.created_at).toLocaleString() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700">{{ comment.body }}</p>
                                </div>
                            </div>
                        </div>
                    </ScrollArea>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
