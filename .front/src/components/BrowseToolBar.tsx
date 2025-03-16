import React from 'react';
import { useNavigate } from 'react-router-dom';
import { Button, Space } from 'antd';
import { ArrowLeftOutlined, ArrowRightOutlined, ReloadOutlined, HomeOutlined } from '@ant-design/icons';

export const BrowseToolBar: React.FC = () => {
    const navigate = useNavigate();
    //const location = useLocation();

    // Проверка, доступен ли переход назад
    const canGoBack = window.history.state?.idx > 0;

    // Проверка, доступен ли переход вперёд
    const canGoForward = window.history.state?.idx < window.history.length - 1;

    // Обработчик для кнопки "Назад"
    const handleGoBack = () => {
        navigate(-1);
    };

    // Обработчик для кнопки "Вперёд"
    const handleGoForward = () => {
        navigate(1);
    };

    // Обработчик для кнопки "Обновить"
    const handleRefresh = () => {
        window.location.reload();
    };

    // Обработчик для кнопки "Домой"
    const handleGoHome = () => {
        navigate('/');
    };

    return (
        <Space>
            <Button
                title="Backward"
                icon={<ArrowLeftOutlined />}
                onClick={handleGoBack}
                disabled={!canGoBack}
            />

            <Button
                title="Forward"
                icon={<ArrowRightOutlined />}
                onClick={handleGoForward}
                disabled={!canGoForward}
            />

            <Button
                title="Refresh"
                icon={<ReloadOutlined />}
                onClick={handleRefresh}
            />


            <Button
                title="Home"
                icon={<HomeOutlined />}
                onClick={handleGoHome}
            />

        </Space>
    );
};
