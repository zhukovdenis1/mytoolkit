import React, {useEffect, useState} from "react";
import {useNavigate, useParams} from "react-router-dom";
import {Button, Form, Input, message, Space, TreeSelect, Editor} from "ui";
import {convertTreeData} from "@/utils/ui";
import {api} from "api"

export const NoteFormPage: React.FC = ({modal}) => {
    const noteId = modal.data?.id || 0;
    const isEditPage = !!noteId;
    const [form] = Form.useForm();
    const [categoriesTree, setCategoriesTree] = useState([]);
    //const { note_id: noteId  } = useParams<{ note_id: number }>();
    const [reloadTrigger, setReloadTrigger] = useState(0);
    const editor = Editor.useEditor();

    const reload = () => {
        setReloadTrigger(prev => prev + 1)
    }

    useEffect(() => {
        const fetchData = async () => {
            let noteResponse = true;
            if (isEditPage) {
                noteResponse = await api.safeRequest(`notes.show`, {note_id: noteId});
                form.setFieldsValue(noteResponse?.data?.data)
                editor.setValue(noteResponse?.data?.data.text)
            }
            modal.setLoading(false)
            const categoriesTreeResponse = await api.safeRequest("notes.categories.tree");
            if (noteResponse === false || categoriesTreeResponse === false) {
                modal.close()
            }
            //setFormData(categoryResponse?.data?.noteCategory)
            setCategoriesTree(convertTreeData(categoriesTreeResponse.data.data, {id: 'value', name: 'title'}));
        };

        fetchData();


    }, [noteId, reloadTrigger]);

    const handleSave = async (values: any) => {
        modal.setLoading(true);
        let formData = {
            ...values,
            text: editor.getValue()
        }
        let success = true;

        const response = isEditPage
            ? await api.safeRequest(`notes.edit`, {
                //...form.getFieldsValue(),
                ...formData,
                note_id: noteId,
                categories: form.getFieldValue('categories') ?? null,
            })
            : await api.safeRequest(`notes.add`, {
                //...form.getFieldsValue(),
                ...formData,
                categories: form.getFieldValue('categories')  ?? null,
            })
        success = response.data?.success

        if (success) {
            message.success("Note saved successfully!");
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
        <>
            <Form form={form} onFinish={handleSave}>
                <Form.Item name="exit" hidden>
                    <Input type="hidden" />
                </Form.Item>
                <Form.Item name="title" label="Title" rules={[{ required: true, message: "Please input title!" }]} >
                    <Input disabled={modal.loading} />
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
                            node.title.toLowerCase().includes(input.toLowerCase())
                        }
                    />
                </Form.Item>
                <Editor editor={editor} disabled={modal.loading} />

                <Form.Item>
                    <Space>
                        <Button type="" htmlType="submit" onClick={() => form.setFieldValue("exit", 0)}>Save</Button>
                        <Button type="" htmlType="submit" onClick={() => form.setFieldValue("exit", 1)}>Save & Close</Button>
                        <Button type="" htmlType="button" onClick={() => exit({reload: true})}>Close & Reload</Button>
                        <Button type="primary" htmlType="submit" onClick={() => form.setFieldValue("exit", 2)}>Save & Reload</Button>
                    </Space>
                </Form.Item>
            </Form>
        </>
    )
}
