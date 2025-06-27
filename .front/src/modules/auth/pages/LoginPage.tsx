import React, { useContext, useState } from "react";
import { useNavigate } from "react-router-dom";
import { AuthContext } from "../AuthProvider.tsx";
import { Form, Input, Button } from "antd";
import { LockOutlined, MailOutlined } from "@ant-design/icons";
import {route} from "api";

export const LoginPage: React.FC = () => {
    const authContext = useContext(AuthContext);
    const navigate = useNavigate();
    const [loading, setLoading] = useState<boolean>(false); // Состояние для лоадера

    const handleSubmit = async (values: { email: string; password: string }) => {
        setLoading(true);
        if (authContext) {
            try {
                await authContext.signin(values, () => navigate(route('user')));
            } catch (error) {
                console.error("Login failed:", error);
            } finally {
                setLoading(false);
            }
        }
    };

    return (
            <div className="login">
                <h1>
                    Login
                </h1>
                <Form
                    name="login"
                    initialValues={{ email: "test@example.com", password: "password" }}
                    onFinish={handleSubmit}
                    layout="vertical"
                >
                    <Form.Item
                        name="email"
                        label="Email"
                        rules={[
                            { required: true, message: "Please input your email!" },
                            { type: "email", message: "Please enter a valid email!" },
                        ]}
                    >
                        <Input
                            prefix={<MailOutlined />}
                            placeholder="Email"
                            disabled={loading} // Отключаем поле ввода при загрузке
                        />
                    </Form.Item>
                    <Form.Item
                        name="password"
                        label="Password"
                        rules={[{ required: true, message: "Please input your password!" }]}
                    >
                        <Input.Password
                            prefix={<LockOutlined />}
                            placeholder="Password"
                            disabled={loading} // Отключаем поле ввода при загрузке
                        />
                    </Form.Item>
                    <Form.Item>
                        <Button
                            type="primary"
                            htmlType="submit"
                            block
                            loading={loading} // Добавляем лоадер на кнопку
                        >
                            Login
                        </Button>
                    </Form.Item>
                </Form>
            </div>
    );
};
