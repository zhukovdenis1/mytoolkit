import React, {useEffect, useState} from "react";
import {Button, Editor, Form, Input, message, Space, DatePicker} from "ui";
import moment from 'moment';
import {api} from "@/services/api.tsx";
import {route} from "api";
import {useNavigate, useParams} from "react-router-dom";

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
                    form.setFieldsValue({
                        published_at: response.data.article?.published_at
                            ? moment(response.data.article?.published_at)
                            : null
                    });
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
                    <Input type="hidden"/>
                </Form.Item>
                <Form.Item name="name" label="Name" rules={[{required: true, message: "Please input name!"}]}>
                    <Input disabled={loading}/>
                </Form.Item>
                <Form.Item name="h1" label="H1" rules={[{required: true, message: "Please input h1 !"}]}>
                    <Input disabled={loading}/>
                </Form.Item>
                <Form.Item name="title" label="Title">
                    <Input disabled={loading}/>
                </Form.Item>
                <Form.Item name="keywords" label="Keywords">
                    <Input disabled={loading}/>
                </Form.Item>
                <Form.Item name="description" label="Description">
                    <Input disabled={loading}/>
                </Form.Item>
                <Form.Item name="code" label="Code">
                    <Input disabled={loading}/>
                </Form.Item>
                <Form.Item name="separation" label="Separation. Ex: intro(1,2);conc(-1)">
                    <Input disabled={loading}/>
                </Form.Item>
                {isEditPage && <Editor
                    editor={editor}
                    disabled={loading}
                    mode="edit"
                    config={{
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

                <Form.Item name="note" label="Note">
                    <Input.TextArea disabled={loading}/>
                </Form.Item>
                <Form.Item name="product_id" label="ID product">
                    <Input disabled={loading}/>
                </Form.Item>
                <Form.Item name="site_id" label="ID site">
                    <Input disabled={loading}/>
                </Form.Item>
                <Form.Item
                    name="published_at"
                    label="Published date"
                    initialValue={moment()} // текущая дата по умолчанию
                >
                    <DatePicker
                        showTime
                        format="DD.MM.YYYY HH:mm:ss"
                        disabled={loading}
                        style={{width: '100%'}}
                    />
                </Form.Item>
                <Form.Item>
                    <Space>
                        <Button disabled={loading} type="default" htmlType="submit"
                                onClick={() => form.setFieldValue("exit", 0)}>Save</Button>
                        <Button disabled={loading} type="primary" htmlType="submit"
                                onClick={() => form.setFieldValue("exit", 1)}>Save & Exit</Button>
                    </Space>
                </Form.Item>
            </Form>

            <p>
                <a
                    href={`https://deshevyi.ru/a-${articleId}`}
                    target="_blank"
                >
                    Ссылка на статью
                </a>
            </p>
            <p>
                Ссылка на товар:{" "}
                <a
                    href={`https://deshevyi.ru/p-${form.getFieldValue('product_id') || ''}/`}
                    target="_blank"
                >
                    https://deshevyi.ru/p-{form.getFieldValue('product_id') || ''}/
                </a>
            </p>
            <p>
                <a
                    href={`https://deshevyi.ru/go?id=${form.getFieldValue('product_id') || ''}`}
                    target="_blank"
                >
                    Ссылка на товар aliexpress
                </a>
            </p>
            <p>
                <a
                    href={`https://deshevyi.ru/go?id=${form.getFieldValue('product_id') || ''}&page_name=reviews`}
                    target="_blank"
                >
                    Ссылка на отзывы aliexpress
                </a>
            </p>

        </>
    );


};

