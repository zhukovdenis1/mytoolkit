import React, { useState, useEffect } from "react";


type EditorHandle = {
    setValue: (value: string) => void;
    getValue: () => string;
    onChange: () => void;
};

const useEditor = (): EditorHandle => {
    const [value, setValue] = useState('');

    return {
        setValue: (val: string) => {alert('y');return setValue(val)},
        getValue: () => value,
        onChange: () => {}
    };
};

type EditorProps = {
    editor: EditorHandle;
};

const EditorComponent: React.FC<EditorProps> = ({ editor }) => {
    const [struc, setStruc] = useState([]);
    const [value, setValue] = useState(editor.getValue());

    useEffect(() => {
        let s = buildStruc(value);
        setStruc(s);
    }, []);
    //}, [value]);

    const buildStruc = (newValue: string) => {
        let s;
        if (newValue) {
            try {
                s = JSON.parse(newValue);
            } catch (e) {
                console.error("Невалидный JSON", e);
                s = [{type: 'html', data: 'default'}];
            }
        } else {
            s = [{type: 'html', data: 'default'}];
        }
        s = s.reduce((acc, item, index) => {
            const key = Math.random().toString(36).substr(2, 9);
            acc[key] = {
                key: key,
                ...item,
                order: index + 1  // Порядковый номер
            };
            return acc;
        }, {});

        return s;
    };
    const changeStruc = (key: string, newValue: string) => {
        const updatedStruc = {
            ...struc,
            [key]: {
                ...struc[key],
                data: newValue // Обновляем значение data
            }
        };
        setStruc(updatedStruc);
        editor.onChange();
    };

    editor.getValue = () => {
        return JSON.stringify(
            Object.values(struc)
                .sort((a, b) => a.order - b.order)
                .map(({ type, data }) => ({ type, data }))
        );
    };

    editor.setValue = (newValue: string) => {
        setValue(newValue);
        setStruc(buildStruc(newValue));
        editor.onChange();
    };

    return (
        <div>
            {struc && Object.keys(struc).length > 0 ? (
                Object.entries(struc).map(([key, item]) => (
                    <textarea
                        key={key}
                        value={item.data}
                        onChange={(e) => changeStruc(key, e.target.value)}
                        style={{ width: "100%", height: "150px" }}
                    />
                ))
            ) : (
                <p>Нет данных</p>
            )}
        </div>
    );
};

// Приведение типа, чтобы PhpStorm не ругался
const Editor = Object.assign(EditorComponent, { useEditor });

Editor.useEditor = useEditor;

export default Editor;
