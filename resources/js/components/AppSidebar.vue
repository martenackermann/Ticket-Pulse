<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { BookOpen, ChevronDown, FolderGit2, LayoutGrid } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import {
    SidebarGroup,
    SidebarGroupLabel,
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { usePage } from '@inertiajs/vue3';
import { dashboard } from '@/routes';
import { show as showBoard } from '@/routes/boards';
import { show as showWorkspace } from '@/routes/workspaces';
import type { NavItem } from '@/types';
import type { Workspace } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

type SharedPageProps = {
    workspaceNavigation?: Workspace[];
};

const page = usePage<SharedPageProps>();

const workspaces = computed(() => page.props.workspaceNavigation ?? []);

const toggleWorkspaceDetails = (event: MouseEvent): void => {
    event.preventDefault();
};
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />

            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel>Workspaces</SidebarGroupLabel>

                <SidebarMenu>
                    <Collapsible
                        v-for="workspace in workspaces"
                        :key="workspace.id"
                        class="group/collapsible"
                        :default-open="false"
                    >
                        <SidebarMenuItem>
                            <CollapsibleTrigger as-child>
                                <SidebarMenuButton as-child>
                                    <div class="flex w-full items-center">
                                        <Link :href="showWorkspace(workspace.id)" class="truncate">
                                            {{ workspace.name }}
                                        </Link>
                                        <button
                                            type="button"
                                            class="ml-auto inline-flex size-6 items-center justify-center rounded transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground"
                                            @click="toggleWorkspaceDetails"
                                        >
                                            <ChevronDown class="size-4 transition-transform group-data-[state=open]/collapsible:rotate-180" />
                                        </button>
                                    </div>
                                </SidebarMenuButton>
                            </CollapsibleTrigger>
                        </SidebarMenuItem>

                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem
                                    v-for="board in workspace.boards ?? []"
                                    :key="board.id"
                                >
                                    <SidebarMenuSubButton as-child>
                                        <Link :href="showBoard(board.id)">
                                            {{ board.name }}
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </Collapsible>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
