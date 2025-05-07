import React, {useEffect, useState} from "react";
import {Button, Editor, Form, Input, message, Space} from "ui";
import {api} from "@/services/api.tsx";
import {route} from "api";
import {useNavigate, useParams} from "react-router-dom";
import {convertTreeData} from "@/utils/ui.ts";

export const ShopArticlesFormPage: React.FC = ({}) => {

    const editor = Editor.useEditor();
    const [form] = Form.useForm();
    const navigate = useNavigate();
    const { article_id: articleId } = useParams();
    const isEditPage = !!articleId;
    const [loading, setLoading] = useState(false);

    /**
     *
     */
    useEffect(() => {
        const fetchData = async () => {
            let response;
            if (isEditPage) {
                setLoading(true);
                response = await api.safeRequestWithAlert(`admin.shop.articles.show`, { article_id: articleId });
                if (response.success) {
                    form.setFieldsValue(response.data.article);
                    editor.setValue(response.data.article.text);
                    setLoading(false);
                }
            }
        };

        fetchData();
    }, [articleId]);

    /**
     *
     * @param values
     */
    const handleSave = async (values: any) => {
        const formData = {
            ...values,
            text: editor.getValue(),
        };

        const response = isEditPage
            ? await api.safeRequestWithAlert(`admin.shop.articles.edit`, {
                ...formData,
                article_id: articleId
            })
            : await api.safeRequestWithAlert(`admin.shop.articles.add`, formData);

        if (response && response.success) {
            message.success("Article saved successfully!");
            if (values.exit) {
                if (isEditPage) {
                    navigate(route('shop.articles'));
                } else {
                    navigate(route('shop.articles.edit', {'article_id': response.data.article.id}));
                }
            }
        }
    };

    return (
        <>
            <Form form={form} onFinish={handleSave}>
                <Form.Item name="exit" hidden>
                    <Input type="hidden" />
                </Form.Item>
                <Form.Item name="name" label="Name" rules={[{ required: true, message: "Please input name!" }]}>
                    <Input disabled={loading}/>
                </Form.Item>
                <Form.Item name="title" label="Title">
                    <Input disabled={loading}/>
                </Form.Item>
                {isEditPage && <Editor
                        editor={editor}
                        disabled={loading}
                        mode="edit"
                        config = {{
                            fileRoutes: {
                                save: {route: 'admin.articles.files.add', data: {article_id: articleId}},
                                delete: {route: 'files.delete'}
                            },
                            image: {
                                storageId: 1
                            }
                        }}
                    />
                }
                <Form.Item>
                    <Space>
                        <Button disabled={loading} type="default" htmlType="submit" onClick={() => form.setFieldValue("exit", 0)}>Save</Button>
                        <Button disabled={loading} type="primary" htmlType="submit" onClick={() => form.setFieldValue("exit", 1)}>Save & Exit</Button>
                    </Space>
                </Form.Item>
            </Form>
        </>
    );


};

