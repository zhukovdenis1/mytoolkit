import { createSlice, createAsyncThunk } from "@reduxjs/toolkit";
import { NoteCategory } from "../types/category";
import { fetchCategories, createCategory, updateCategory, deleteCategory } from "../services/categoryService";

interface CategoryState {
    categories: NoteCategory[];
    loading: boolean;
    error: string | null;
}

const initialState: CategoryState = {
    categories: [],
    loading: false,
    error: null,
};

// Загружаем категории
export const loadCategories = createAsyncThunk("categories/load", fetchCategories);

// Добавляем категорию
export const addCategory = createAsyncThunk("categories/add", createCategory);

// Редактируем категорию
export const editCategory = createAsyncThunk(
    "categories/edit",
    async ({ id, data }: { id: number; data: Partial<NoteCategory> }) => {
        return await updateCategory(id, data);
    }
);

// Удаляем категорию
export const removeCategory = createAsyncThunk(
    "categories/remove",
    async (id: number, { rejectWithValue }) => {
        try {
            await deleteCategory(id);
            return id; // Возвращаем ID удаленной категории
        } catch (error: any) {
            return rejectWithValue(error.response?.data || "Failed to delete category.");
        }
    }
);

const categorySlice = createSlice({
    name: "categories",
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            // Загрузка категорий
            .addCase(loadCategories.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(loadCategories.fulfilled, (state, action) => {
                state.loading = false;
                state.categories = action.payload;
            })
            .addCase(loadCategories.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || "Failed to load categories.";
            })

            // Добавление категории
            .addCase(addCategory.fulfilled, (state, action) => {
                state.categories.push(action.payload);
            })

            // Редактирование категории
            .addCase(editCategory.fulfilled, (state, action) => {
                const index = state.categories.findIndex((cat) => cat.id === action.payload.id);
                if (index !== -1) {
                    state.categories[index] = action.payload;
                }
            })

            // Удаление категории
            .addCase(removeCategory.fulfilled, (state, action) => {
                state.categories = state.categories.filter((cat) => cat.id !== action.payload);
            })

            .addCase(removeCategory.rejected, (state, action) => {
                state.error = action.error.message || "Failed to delete category.";
            });
    },
});

export default categorySlice.reducer;
