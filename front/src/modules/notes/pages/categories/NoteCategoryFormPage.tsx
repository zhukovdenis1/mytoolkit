import React, { useState, useEffect } from "react";
import { Form, TreeSelect, Input, Button, message, Space } from "ui";
import { api } from "api";
import {convertTreeData} from "@/utils/ui.ts";

interface NoteCategoryFormPageProps {
    modal?: {
        data?: {
            id?: number;
            parentId?: number;
        };
        setLoading: (loading: boolean) => void;
        close: (data?: { reload: boolean }) => void;
        loading: boolean;
    };
}


export const NoteCategoryFormPage: React.FC<NoteCategoryFormPageProps> = ({ modal = {
    data: {
        id: 0,
        parentId: 0
    },
    setLoading: (_loading: boolean) => {},
    close: (_data?: { reload: boolean }) => {},
    loading: false,
} }) => {
    const categoryId = modal.data?.id || 0;
    const parentId = modal.data?.parentId || 0;
    const isEditPage = !!categoryId;
    const [form] = Form.useForm();
    const [categoriesTree, setCategoriesTree] = useState<any[]>([]);
    const [reloadTrigger, setReloadTrigger] = useState(0);

    const reload = () => {
        setReloadTrigger(prev => prev + 1);
    };

    useEffect(() => {
        const fetchData = async () => {
            let categoryResponse;
            if (isEditPage) {
                categoryResponse = await api.safeRequest(`notes.categories.show`, { category_id: categoryId });
                if (categoryResponse && typeof categoryResponse !== 'boolean' && categoryResponse.data) {
                    form.setFieldsValue(categoryResponse.data.noteCategory);
                }
            } else if (parentId) {
                form.setFieldValue('parent_id', parentId);
            }
            modal.setLoading(false);
            const treeResponse = await api.safeRequest("notes.categories.tree");
            if (categoryResponse === false || treeResponse === false) {
                modal.close();
            }
            if (treeResponse && typeof treeResponse !== 'boolean' && treeResponse.data) {
                setCategoriesTree(convertTreeData(treeResponse.data.data, { id: "value", name: "title" }));
            }
        };

        fetchData();
    }, [categoryId, reloadTrigger]);

    const handleSave = async (values: any) => {
        modal.setLoading(true);
        let success = false;

        const response = isEditPage
            ? await api.safeRequest(`notes.categories.edit`, {
                category_id: categoryId,
                ...form.getFieldsValue()
            })
            : await api.safeRequest(`notes.categories.add`, {
                ...form.getFieldsValue()
            });

        if (response && typeof response !== 'boolean' && response.data) {
            success = response.data.success;
            if (success) {
                message.success("Category saved successfully!");
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
        <div>
            <Form form={form} onFinish={handleSave}>
                <Form.Item name="exit" hidden>
                    <Input type="hidden" />
                </Form.Item>
                <Form.Item name="name" label="Name" rules={[{ required: true, message: "Please input name!" }]}>
                    <Input disabled={modal.loading} autoComplete="off" />
                </Form.Item>
                <Form.Item label="Parent category" name='parent_id'>
                    <TreeSelect
                        disabled={modal.loading}
                        treeData={categoriesTree}
                        placeholder='Please select'
                        allowClear
                        treeDefaultExpandAll
                    />
                </Form.Item>
                <Form.Item>
                    <Space>
                        <Button type="default" htmlType="submit" onClick={() => form.setFieldValue("exit", 0)}>Save</Button>
                        <Button type="default" htmlType="submit" onClick={() => form.setFieldValue("exit", 1)}>Save & Close</Button>
                        <Button type="default" htmlType="button" onClick={() => exit({ reload: true })}>Close & Reload</Button>
                        <Button type="primary" htmlType="submit" onClick={() => form.setFieldValue("exit", 2)}>Save & Reload</Button>
                    </Space>
                </Form.Item>
            </Form>
        </div>
    );
};
