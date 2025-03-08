import React, { useEffect, useState } from "react";
import { Form, Input, Button, Space, TreeSelect, message, SearchInput } from "ui";
import { api } from "api";
import { convertTreeData } from "@/utils/ui";
import { Editor } from "ui";
import {SubNoteList} from "../components/SubNoteList";

interface NoteFormPageProps {
    modal?: {
        data?: {
            id?: number;
        };
        setLoading: (loading: boolean) => void;
        close: (data?: { reload: boolean }) => void;
        loading: boolean;
    };
}

export const NoteFormPage: React.FC<NoteFormPageProps> = ({ modal = {
    data: {id: 0},
    setLoading: (_loading: boolean) => {},
    close: (_data?: {reload: boolean}) => {},
    loading: false
} }) => {
    console.log('*', modal.data)
    const noteId = modal.data?.id || 0;
    const isEditPage = !!noteId;
    const [form] = Form.useForm();
    const [categoriesTree, setCategoriesTree] = useState<any[]>([]);
    const [reloadTrigger, setReloadTrigger] = useState(0);
    const editor = Editor.useEditor();

    const reload = () => {
        setReloadTrigger(prev => prev + 1);
    };

    useEffect(() => {
        const fetchData = async () => {
            let noteResponse;
            if (isEditPage) {
                noteResponse = await api.safeRequest(`notes.show`, { note_id: noteId });
                if (noteResponse && typeof noteResponse !== 'boolean' && noteResponse.data) {
                    form.setFieldsValue(noteResponse.data.data);
                    editor.setValue(noteResponse.data.data.text);
                }
            } else {
                form.setFieldsValue(modal.data);
            }
            modal.setLoading(false);
            const categoriesTreeResponse = await api.safeRequest("notes.categories.tree");
            if (noteResponse === false || categoriesTreeResponse === false) {
                modal.close();
            }
            if (categoriesTreeResponse && typeof categoriesTreeResponse !== 'boolean' && categoriesTreeResponse.data) {
                setCategoriesTree(convertTreeData(categoriesTreeResponse.data.data, { id: 'value', name: 'title' }));
            }
        };

        fetchData();
    }, [noteId, reloadTrigger]);

    const handleSave = async (values: any) => {
        modal.setLoading(true);
        const formData = {
            ...values,
            text: editor.getValue(),
        };
        let success = false;

        const response = isEditPage
            ? await api.safeRequest(`notes.edit`, {
                ...formData,
                note_id: noteId,
                categories: form.getFieldValue('categories') ?? null,
            })
            : await api.safeRequest(`notes.add`, {
                ...formData,
                categories: form.getFieldValue('categories') ?? null,
            });

        if (response && typeof response !== 'boolean' && response.data) {
            success = response.data.success;
            if (success) {
                message.success("Note saved successfully!");
            } else {
                message.error("Data wasn't changed");
            }
        }

        if (values.exit && success) {
            exit({ reload: values.exit == 2 });
        } else {
            reload();
        }
    };

    const exit = (data?: { reload: boolean }) => {
        modal.close(data);
    };

    return (
        <>
            <Form form={form} onFinish={handleSave}>
                <Form.Item name="exit" hidden>
                    <Input type="hidden" />
                </Form.Item>
                <Form.Item name="title" label="Title" rules={[{ required: true, message: "Please input title!" }]}>
                    <Input disabled={modal.loading} />
                </Form.Item>
                <Form.Item name="parent_id" label="Parent">
                    <SearchInput route="notes.dropdown" placeholder="Please select" />
                </Form.Item>
                <Form.Item label="Categories" name='categories'>
                    <TreeSelect
                        disabled={modal.loading}
                        showSearch
                        style={{ width: '100%' }}
                        dropdownStyle={{ maxHeight: 400, overflow: 'auto' }}
                        placeholder="Please select"
                        allowClear
                        multiple
                        treeDefaultExpandAll
                        treeData={categoriesTree}
                        filterTreeNode={(input, node) =>
                            (node.title as string).toLowerCase().includes(input.toLowerCase())
                        }
                    />
                </Form.Item>
                <SubNoteList parentId={noteId} />
                <Editor editor={editor} disabled={modal.loading} />
                <Form.Item>
                    <Space>
                        <Button type="default" htmlType="submit" onClick={() => form.setFieldValue("exit", 0)}>Save</Button>
                        <Button type="default" htmlType="submit" onClick={() => form.setFieldValue("exit", 1)}>Save & Close</Button>
                        <Button type="default" htmlType="button" onClick={() => exit({ reload: true })}>Close & Reload</Button>
                        <Button type="primary" htmlType="submit" onClick={() => form.setFieldValue("exit", 2)}>Save & Reload</Button>
                    </Space>
                </Form.Item>
            </Form>

        </>
    );
};
