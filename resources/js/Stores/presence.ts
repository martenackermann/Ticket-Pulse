import { defineStore } from 'pinia';
import { ref } from 'vue';
import { User } from '@/types';

export const usePresenceStore = defineStore('presence', () => {
    const users = ref<User[]>([]);

    function setUsers(newUsers: User[]) {
        users.value = newUsers;
    }

    function addUser(user: User) {
        if (!users.value.find(u => u.id === user.id)) {
            users.value.push(user);
        }
    }

    function removeUser(user: User) {
        users.value = users.value.filter(u => u.id !== user.id);
    }

    return {
        users,
        setUsers,
        addUser,
        removeUser
    };
});
