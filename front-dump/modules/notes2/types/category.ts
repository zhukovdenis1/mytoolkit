export interface NoteCategory {
    id: number;
    parentId: number | null;
    name: string;
}

export interface ApiResponse<T> {
    data: T;
    success: boolean;
    errors?: Record<string, string[]>;
}
