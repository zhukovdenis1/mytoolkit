import React, {useEffect, useState} from "react";
import {Button, Checkbox, Form, Input, message, Space, Select} from "ui";
import {api} from "@/services/api.tsx";
import {route} from "api";
import {useNavigate} from "react-router-dom";

interface Category {
    id: string;
    name: string;
    active?: number; // если это поле есть в данных
}

export const ShopParsingFormPage: React.FC = ({}) => {

    const [form] = Form.useForm();
    const navigate = useNavigate();
    let [categories, setCategories] = useState<Category[]>([]);



    /**
     *
     */
    useEffect(() => {
        const fetchData = async () => {
            let response = await api.safeRequestWithAlert(`admin.shop.parsing.getEpnCategories`);
            if (response.success) {
                const activeCategories = response.data.data.filter((category: Category) => category.active === 1);
                setCategories(activeCategories)
            }
        };
        fetchData();
    }, []);

    /**
     *
     * @param values
     */
    const handleSave = async (values: any) => {
        const formData = {
            ...values,
            //text: editor.getValue(),
        };

        const response = await api.safeRequestWithAlert(`admin.shop.parsing.add`, formData);

        if (response && response.success) {
            console.log(response)
            message.success(response.data.data.message);
            if (values.exit) {
                navigate(route('shop.parsing'));
            }
        }
    };

    return (
        <>
            <Form form={form} onFinish={handleSave}>
                <Form.Item name="exit" hidden>
                    <Input type="hidden" />
                </Form.Item>
                <Form.Item
                    name="important"
                    label="Importat"
                    valuePropName="checked" // Обязательно для Checkbox в Form
                    initialValue={false} // Всегда будет false при создании формы
                >
                    <Checkbox></Checkbox>
                </Form.Item>

                <Form.Item
                    name="category_id"
                    label="Категория epn"
                >
                    <Select
                        placeholder="Выберите категорию"
                        options={[/*{ value: '0', label: 'Все категории' },*/ ...categories.map(category => ({
                            value: category.id,
                            label: category.name
                        }))]}
                    />
                </Form.Item>

                <Form.Item
                    name="data"
                    label="Data"
                    rules={[{ required: true, message: 'Пожалуйста, id_ae через Enter!' }]}
                >
                    <Input.TextArea
                        rows={4} // Количество строк по умолчанию
                        placeholder="Введите текст id_ae черзе Enter или xml"
                    />
                </Form.Item>

                <Form.Item>
                    <Space>
                        <Button type="primary" htmlType="submit" onClick={() => form.setFieldValue("exit", 1)}>Save & Exit</Button>
                    </Space>
                </Form.Item>
            </Form>
        </>
    );


};

