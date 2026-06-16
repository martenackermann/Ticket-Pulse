import { defineStore } from 'pinia';
import { ref } from 'vue';
import { Activity } from '@/types';

export const useActivityStore = defineStore('activity', () => {
    const activities = ref<Activity[]>([]);

    function setActivities(newActivities: Activity[]) {
        activities.value = newActivities;
    }

    function addActivity(activity: Activity) {
        activities.value.unshift(activity);
        if (activities.value.length > 50) {
            activities.value.pop();
        }
    }

    return {
        activities,
        setActivities,
        addActivity
    };
});
