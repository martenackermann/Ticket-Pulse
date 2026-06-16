import {Component} from "vue";
import {RouteDefinition} from "@/wayfinder";

export enum CardStatus {
    Todo = 'todo',
    InProgress = 'in_progress',
    Done = 'done',
}

export interface User {
    id: number;
    name: string;
    email: string;
}

export interface Comment {
    id: number;
    card_id: number;
    user_id: number;
    body: string;
    user?: User;
    created_at: string;
}

export interface Card {
    id: number;
    board_id: number;
    title: string;
    description: string | null;
    status: CardStatus;
    position: number;
    comments?: Comment[];
}

export interface Activity {
    id: number;
    board_id: number;
    user_id: number;
    event_type: string;
    payload: any;
    user?: User;
    created_at: string;
}

export interface Board {
    id: number;
    workspace_id: number;
    name: string;
    cards?: Card[];
    activities?: Activity[];
}

export interface Workspace {
    id: number;
    name: string;
    owner_id: number;
    boards?: Board[];
}

export interface NavItem {
    title: string;
    href: RouteDefinition<any>;
    icon: Component;
}
