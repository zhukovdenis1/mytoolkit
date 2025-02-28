import React, { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { useAppDispatch, useAppSelector } from "../../../store/hooks";
import { addCategory, editCategory, loadCategories } from "../store/categorySlice";
import { NoteCategory } from "../types/category";

const EditCategoryPage: React.FC = () => {
    const { id } = useParams<{ id: string }>();
    const isEdit = Boolean(id);
    const dispatch = useAppDispatch();
    const navigate = useNavigate();
    const categories = useAppSelector((state) => state.categories.categories);

    const [formData, setFormData] = useState<Partial<NoteCategory>>({
        name: "",
        parentId: null,
    });
    const [errors, setErrors] = useState<Record<string, string[]>>({});
    const [notification, setNotification] = useState<string | null>(null);

    useEffect(() => {
        if (isEdit) {
            const category = categories.find((cat) => cat.id === Number(id));
            if (category) {
                setFormData({ name: category.name, parentId: category.parentId });
            }
        } else {
            dispatch(loadCategories());
        }
    }, [dispatch, isEdit, id, categories]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        const { name, value } = e.target;
        setFormData((prev) => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (saveAndReturn: boolean) => {
        setErrors({});
        try {
            if (isEdit) {
                await dispatch(editCategory({ id: Number(id), data: formData })).unwrap();
            } else {
                await dispatch(addCategory(formData)).unwrap();
            }
            setNotification("Saved successfully!");

            if (saveAndReturn) {
                setTimeout(() => navigate("/notes2"), 2000);
            }
        } catch (error: any) {
            if (error.errors) {
                setErrors(error.errors);
            } else {
                setNotification("Failed to save.");
            }
        }
    };

    const handleCancel = () => navigate("/notes2");

    return (
        <div>
            <h1>{isEdit ? "Edit" : "Add"} Category</h1>
            {notification && <div>{notification}</div>}
            <form>
                <div>
                    <label>
                        Name:
                        <input
                            type="text"
                            name="name"
                            value={formData.name || ""}
                            onChange={handleChange}
                            className={errors.name ? "error-field" : ""}
                        />
                    </label>
                    {errors.name && <div>{errors.name.join(", ")}</div>}
                </div>
                <div>
                    <label>
                        Parent:
                        <select
                            name="parentId"
                            value={formData.parentId || ""}
                            onChange={handleChange}
                        >
                            <option value="">None</option>
                            {categories.map((cat) => (
                                <option key={cat.id} value={cat.id}>
                                    {cat.name}
                                </option>
                            ))}
                        </select>
                    </label>
                </div>
                <button type="button" onClick={() => handleSubmit(false)}>
                    Save
                </button>
                <button type="button" onClick={() => handleSubmit(true)}>
                    Save and Return
                </button>
                <button type="button" onClick={handleCancel}>
                    Cancel
                </button>
            </form>
        </div>
    );
};

export default EditCategoryPage;
