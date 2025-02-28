import React, {useState, useEffect} from "react";
import {api} from "api";
import {Form, TreeSelect, Input, Button,  message, Space, showModal} from "ui"


export const NoteCategoryFormPage: React.FC = ({ modal }) => {
    const categoryId = modal.data?.id || 0;
    const parentId = modal.data?.parentId || 0;
    const isEditPage = !!categoryId;
    const [form] = Form.useForm();
    const [categoriesTree, setCategoriesTree] = useState([]);
    const [reloadTrigger, setReloadTrigger] = useState(0);

    const reload = () => {
        setReloadTrigger(prev => prev + 1)
    }

    useEffect(() => {
        const fetchData = async () => {
            let categoryResponse = true;
            if (isEditPage) {
                categoryResponse = await api.safeRequest(`notes.categories.show`, {category_id: categoryId});
                form.setFieldsValue(categoryResponse?.data.noteCategory)
            } else if (parentId) {
                form.setFieldValue('parent_id', parentId);
            }
            modal.setLoading(false)
            const treeResponse = await api.safeRequest("notes.categories.tree");
            if (categoryResponse === false || treeResponse === false) {
                modal.close()
            }
            //setFormData(categoryResponse?.data?.noteCategory)
            setCategoriesTree(prepareDataForTree(treeResponse?.data.data));
        };

        fetchData();


    }, [categoryId, reloadTrigger]);

    const handleSave = async (values: any) => {
        modal.setLoading(true)
        let success = true;

        const response = isEditPage
            ? await api.safeRequest(`notes.categories.edit`, {
                category_id: categoryId,
                ...form.getFieldsValue()
                //...formData
            })
            : await api.safeRequest(`notes.categories.add`, {
                ...form.getFieldsValue()
            })
        success = response.data.success
        if (success) {
            message.success("Category saved successfully!");
        } else {
            message.error("Data wasn't changed");
        }

        if (values.exit && success) {
            exit({reload: values.exit == 2})
        } else {
            reload()
        }
    };

    const exit = (data) => {
        modal.close(data)
    }

    return (
        <div>
            <Form form={form} onFinish={handleSave} /*initialValues={formData}*/>
                <Form.Item name="exit" hidden>
                    <Input type="hidden" />
                </Form.Item>
                <Form.Item name="name" label="Name" rules={[{ required: true, message: "Please input name!" }]} >
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
                        <Button type="" htmlType="submit" onClick={() => form.setFieldValue("exit", 0)}>Save</Button>
                        <Button type="" htmlType="submit" onClick={() => form.setFieldValue("exit", 1)}>Save & Close</Button>
                        <Button type="" htmlType="button" onClick={() => exit({reload: true})}>Close & Reload</Button>
                        <Button type="primary" htmlType="submit" onClick={() => form.setFieldValue("exit", 2)}>Save & Reload</Button>
                    </Space>
                </Form.Item>
            </Form>

        </div>
    )
};

const prepareDataForTree = (data: any[]) => {
    return data.map(item => {
        const { id, name } = item;
        return {
            value: id.toString(),
            title: name.toString(),
            children: item.children ? prepareDataForTree(item.children) : undefined,
        };
    });
};
