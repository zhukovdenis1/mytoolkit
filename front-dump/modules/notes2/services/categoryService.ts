import api from "../../../services/api";
import { NoteCategory } from "../types/category";

// Тип для ответа POST и PUT
interface NoteCategoryResponse {
    noteCategory: NoteCategory;
}

// Тип для ответа GET /note/categories
interface NoteCategoriesResponse {
    data: NoteCategory[];
}

// Получение всех категорий
export const fetchCategories = async (): Promise<NoteCategory[]> => {
    const response = await api.get<NoteCategoriesResponse>("/note/categories");
    return response.data.data; // Здесь `data` — это массив категорий
};

// Создание новой категории
export const createCategory = async (data: Partial<NoteCategory>): Promise<NoteCategory> => {
    const response = await api.post<NoteCategoryResponse>("/note/categories", data);
    return response.data.noteCategory; // Здесь `noteCategory` — это созданная категория
};

// Обновление категории
export const updateCategory = async (id: number, data: Partial<NoteCategory>): Promise<NoteCategory> => {
    const response = await api.put<NoteCategoryResponse>(`/note/categories/${id}`, data);
    return response.data.noteCategory; // Здесь `noteCategory` — это обновленная категория
};

// Удаление категории
export const deleteCategory = async (id: number): Promise<void> => {
    const response = await api.delete<{ success: boolean }>(`/note/categories/${id}`);
    if (!response.data.success) {
        throw new Error("Ошибка удаления категории");
    }
};
