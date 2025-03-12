import { useState } from 'react';
import { Popover, Button } from 'antd';
import { TooltipProps } from 'antd/lib/tooltip'; // Тип для trigger

interface ConfirmableProps {
    onConfirm: () => void;
    children: React.ReactNode;
    trigger?: TooltipProps['trigger']; // Тип для trigger, который будет совпадать с тем, что принимает Popover
}

export const Confirmable = ({ onConfirm, children, trigger = 'click' }: ConfirmableProps) => {
    const [open, setOpen] = useState(false); // Изменено с 'visible' на 'open'

    const handleConfirm = () => {
        onConfirm(); // Вызываем функцию подтверждения
        setOpen(false); // Скрываем поповер после подтверждения
    };

    const handleCancel = () => {
        setOpen(false); // Скрываем поповер при отмене
    };

    const content = (
        <div>
            <span>Are you sure?</span>
            <Button type="link" onClick={handleConfirm}>
                Yes
            </Button>

            <Button type="primary" onClick={handleCancel}>
                No
            </Button>
        </div>
    );

    return (
        <Popover
            content={content}
            // title="Confirmation" // Уберите или раскомментируйте, если нужно
            trigger={trigger} // Позволяем передавать триггер
            open={open}
            onOpenChange={setOpen} // Обновлено на onOpenChange
        >
            <span style={{ cursor: 'pointer' }}>{children}</span> {/* Оборачиваем детей в span для кликабельности */}
        </Popover>
    );
};
