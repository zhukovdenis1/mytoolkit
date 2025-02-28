import React, { useState, useEffect, useRef } from "react";
import CodeEditor from "@/components/ui/editor/CodeEditor";
import VisualEditor from "@/components/ui/editor/VisualEditor";
import EditorToolBar from "@/components/ui/editor/EditorToolBar"
import { html } from "js-beautify";



type EditorHandle = {
    setValue: (value: string) => void;
    edit: (key: string, data: object) => void;
    add: (key: string, data: object) => void;
    delete: (key: string) => void;
    move: (key: string, step:number) => void;
    debug: (key: string) => void;
    beautify: (key: string) => void;
    getValue: () => string;
    getBoxType: (key:string) => string;
    onChange: () => void;
    //decode: (key: string) => string;
};

const useEditor = (): EditorHandle => {
    const [value, setValue] = useState('');

    return {
        setValue: (val: string) => {alert('y');return setValue(val)},
        edit: (key: string, data:object) => {},
        add: (key: string, data:object) => {},
        delete: (key: string) => {},
        move: (key: string, step:number) => {},
        debug: (key: string) => {},
        beautify: (key: string) => {},
        getValue: () => value,
        onChange: () => {},
        /*decode: (encoded: string|undefined) => {
            return 'not used';
            // let result = '';
            // if (encoded != undefined) {
            //     result = 'error';
            //     try {
            //         const json = JSON.parse(encoded);
            //         console.log(json);
            //         result = json.map(item => `<div class="${item.type}">${item.data}</div>`).join('');
            //     } catch (e) {
            //
            //     }
            //
            // }
            // return result;
        },*/
        getBoxType: (key: string) => {}
    }
};

type EditorProps = {
    editor: EditorHandle;
    disabled?: boolean;
    mode?: string;
};

const uid = () => Math.random().toString(36).substr(2, 9);

const EditorComponent: React.FC<EditorProps> = ({ editor, disabled = false, mode = 'edit' }) => {
    const [structure, setStructure] = useState([]);
    const [value, setValue] = useState(editor.getValue());

    useEffect(() => {
        let s = buildStructure(value);
        setStructure(s);
    }, []);
    //}, [value]);

    const buildStructure = (newValue: string) => {
        let s;
        if (newValue) {
            try {
                s = JSON.parse(newValue);
            } catch (e) {
                console.error("Невалидный JSON", e);
                s = [{type: 'html', data: newValue}];
            }
        } else {
            s = [{type: 'visual', data: ''}];
        }
        s = s.reduce((acc, item, index) => {
            const key = uid();
            acc[key] = {
                key: key,
                ...item,
                order: index + 1  // Порядковый номер
            };
            return acc;
        }, {});

        return s;
    };



    editor.edit = (key: string, data: object /*newData: string, newType: string, newOrder: number*/) => {
        if (data?.type) {
            if (structure[key].type == 'visual' && !data.data) {
                data.data = beautify(structure[key].data)
            }
        }
        const updatedStructure = {
            ...structure,
            [key]: {
                ...structure[key],
                ...data
            }
        };
        setStructure(updatedStructure);
        editor.onChange();
    };

    editor.add = (afterKey: string, data: object /*newValue: string, newType: string, newOrder: number*/) => {
        const newKey = uid()
        const newNode = {
            key: newKey,
            data: '',
            type: 'visual',
            order: structure[afterKey]?.order + 1 || 0,
            ...data,
        }

        // Найдём индекс элемента с ключом afterKey
        const afterKeyIndex = Object.keys(structure).indexOf(afterKey);

        // Получаем все элементы до и после
        const beforeNodes = Object.entries(structure).slice(0, afterKeyIndex + 1);  // Включаем элемент с afterKey
        const afterNodes = Object.entries(structure).slice(afterKeyIndex + 1);

        // Увеличиваем order всех элементов после вставляемой ноды
        const updatedStructure = {
            ...Object.fromEntries(beforeNodes),
            [newKey]: newNode,  // Добавляем новую ноду
            ...Object.fromEntries(afterNodes.map(([key, node]) => [
                key,
                { ...node, order: node.order + 1 }  // Увеличиваем order для всех последующих элементов
            ]))
        };

        // Пересортируем структуру по order
        const sortedStructure = Object.entries(updatedStructure)
            .sort(([, a], [, b]) => a.order - b.order) // Сортируем по order
            .reduce((acc, [key, value]) => {
                acc[key] = value;
                return acc;
            }, {});

        setStructure(sortedStructure);
        editor.onChange();
    };

    // editor.move = (key: string, step: number) => {
    //     if (!structure[key] || step === 0) return; // Проверяем существование элемента и отсутствие движения
    //
    //     const entries = Object.entries(structure);
    //     const index = entries.findIndex(([k]) => k === key);
    //     if (index === -1) return; // Если ключ не найден, выходим
    //
    //     const newIndex = Math.max(0, Math.min(index + step, entries.length - 1)); // Ограничиваем диапазон
    //
    //     if (index === newIndex) return; // Если позиция не изменилась, ничего не делаем
    //
    //     // Перемещаем элемент
    //     const newEntries = [...entries];
    //     const [movedItem] = newEntries.splice(index, 1); // Удаляем элемент
    //     newEntries.splice(newIndex, 0, movedItem); // Вставляем на новое место
    //
    //     // Пересчитываем order
    //     const updatedStructure = Object.fromEntries(
    //         newEntries.map(([k, v], i) => [k, { ...v, order: i }])
    //     );
    //
    //     setStructure(updatedStructure);
    //     editor.onChange();
    // };

    editor.move = (key: string, step: number) => {
        if (!structure[key] || step === 0) return; // Проверяем существование элемента и отсутствие движения

        const entries = Object.entries(structure).sort(([, a], [, b]) => a.order - b.order); // Сортируем по order
        const index = entries.findIndex(([k]) => k === key);
        if (index === -1) return; // Если ключ не найден, выходим

        const newIndex = Math.max(0, Math.min(index + step, entries.length - 1)); // Ограничиваем диапазон
        console.log(index+'-'+newIndex)
        if (index === newIndex) return; // Если позиция не изменилась, ничего не делаем

        const targetKey = entries[newIndex][0]; // Ключ элемента, с которым будем менять местами

        // Меняем местами order текущего и целевого элемента
        const updatedStructure = {
            ...structure,
            [key]: { ...structure[key], order: structure[targetKey].order },
            [targetKey]: { ...structure[targetKey], order: structure[key].order },
        };

        // Пересортируем структуру по order, начиная с 1
        const sortedStructure = Object.fromEntries(
            Object.entries(updatedStructure)
                .sort(([, a], [, b]) => a.order - b.order)
                .map(([k, v], i) => [k, { ...v, order: i + 1 }]) // order начинается с 1
        );

        setStructure(sortedStructure);
        editor.onChange();
    };





    editor.delete = (key: string) => {
        if (!structure[key]) return; // Проверяем, есть ли элемент с таким ключом

        const orderToRemove = structure[key].order;

        // Удаляем элемент из структуры
        const updatedStructure = Object.entries(structure)
            .filter(([k]) => k !== key) // Убираем элемент с указанным ключом
            .map(([k, node]) => [
                k,
                {
                    ...node,
                    order: node.order > orderToRemove ? node.order - 1 : node.order, // Смещаем order
                },
            ])
            .sort(([, a], [, b]) => a.order - b.order) // Сортируем по order
            .reduce((acc, [k, v]) => {
                acc[k] = v;
                return acc;
            }, {});

        setStructure(updatedStructure);
        editor.onChange();
    };

    editor.debug = (key: string) => {
        console.log(structure)
    };

    editor.beautify = (key: string) => {
        if (!structure[key]) return; // Проверяем существование элемента

        console.log(structure[key]);

        let code = beautify(structure[key].data);

        editor.edit(key, {data: code})

    };

    editor.getValue = () => {
        return JSON.stringify(
            Object.values(structure)
                .sort((a, b) => a.order - b.order)
                .map(({ type, data }) => ({ type, data }))
        );
    };

    editor.setValue = (newValue: string) => {
        setValue(newValue);
        setStructure(buildStructure(newValue));
        editor.onChange();
    };


    return (
        <div className={`editor editor-${mode}`}>
            {structure && Object.keys(structure).length > 0 ? (
                Object.entries(structure).map(([key, item]) => {
                    if (item.type == 'visual') {
                        return (
                            // <textarea
                            //     key={key}
                            //     value={item.data}
                            //     onChange={(e) => edit(key, e.target.value)}
                            //     style={{ width: "100%", height: "150px" }}
                            // />
                            <div key={key}>
                                <VisualEditor
                                    value={item.data}
                                    onChange={(val) => editor.edit(key, {data: val})}
                                    disabled={disabled}
                                />
                                <EditorToolBar boxKey={key} editor={editor} mode={mode}/>
                            </div>
                        );
                    } else {
                        return (
                            <div key={key}>
                                <CodeEditor
                                    value={item.data}
                                    onChange={(val) => editor.edit(key, {data: val})}
                                    type={item.type}
                                    disabled={disabled}
                                />
                                <EditorToolBar boxKey={key} editor={editor} mode={mode}/>
                            </div>
                        );
                    }

                })
            ) : (
                <p>Нет данных</p>
            )}
        </div>
    );

};

// Приведение типа, чтобы PhpStorm не ругался
const Editor = Object.assign(EditorComponent, { useEditor });

Editor.useEditor = useEditor;

const beautify = (code) => {
    try {
        return html(code, {
            indent_size: 2,         // Размер отступов
            max_preserve_newlines: 1, // Максимальное количество переносов строк
            preserve_newlines: true,  // Сохранение переносов
        });
    } catch (error) {
        console.error('Error formatting code:', error);
    }
    return code;
}

export default Editor;
