import '@/css/ui/editor.css';
import React, { useState, useEffect } from "react";
import CodeEditor, {CodeEditorData}  from "./CodeEditor";
import VisualEditor from "./VisualEditor/VisualEditor";
import EditorToolBar from "./EditorToolBar";
import VideoEditor, {VideoEditorData} from "./VideoEditor";
import ImageEditor, {ImageEditorData} from "./ImageEditor";
import { html } from "js-beautify";
import {VisualEditorData} from './VisualEditor/types'
import {FileRouts} from './types'
import {api} from "@/services/api.tsx";

type DataObject = Record<string, any>;
interface StructureItem {
    key: string;
    order: number;
    type: string;
    data: DataObject;
}

export type EditorHandle = {
    mode: string;
    //fileRoutes: FileRouts;
    setValue: (value: string) => void;
    edit: (key: string, data: Partial<StructureItem>) => void;
    add: (key: string, data: Partial<StructureItem>) => void;
    delete: (key: string) => void;
    move: (key: string, step: number) => void;
    debug: (key: string) => void;
    beautify: (key: string) => void;
    getValue: () => string;
    getBox: (key: string) => StructureItem;
    //getBoxType: (key: string) => string;
    //onChange: () => void;
    undo: () => void;
    redo: () => void;
    isUndoAvailable: () => boolean;
    isRedoAvailable: () => boolean;
    getHistory: () => Record<string, StructureItem>[];
    getHistoryIndex: () => number;
    setHistory: (value: Record<string, StructureItem>[]) => void;
    setHistoryIndex: (value: number) => void;
};

const useEditor = (): {
    mode: string;
    add: () => void;
    move: () => void;
    debug: () => void;
    edit: () => void;
    beautify: () => void;
    redo: () => void;
    isUndoAvailable: () => boolean;
    setHistory: (value: (((prevState: Record<string, StructureItem>[]) => Record<string, StructureItem>[]) | Record<string, StructureItem>[])) => void;
    delete: () => void;
    setHistoryIndex: (value: (((prevState: number) => number) | number)) => void;
    getValue: () => string;
    getBox: () => StructureItem;
    isRedoAvailable: () => boolean;
    undo: () => void;
    getHistoryIndex: () => number;
    //getBoxType: () => string;
    getHistory: () => Record<string, StructureItem>[];
    setValue: (value: (((prevState: string) => string) | string)) => void
} => {
    const [value, setValue] = useState("");
    const [history, setHistory] = useState<Record<string, StructureItem>[]>([]); // История изменений
    const [historyIndex, setHistoryIndex] = useState(-1); // Текущий индекс в истории

    return {
        mode: 'edit',
        setValue: setValue,
        edit: () => {},
        add: () => {},
        delete: () => {},
        move: () => {},
        debug: () => {},
        beautify: () => {},
        getValue: () => value,
        getBox: () => ({ key: '', order: 0, type: '', data: {} }),
        //getBoxType: () => "",
        //onChange: () => null,
        undo: () => {},
        redo: () => {},
        isUndoAvailable: () => historyIndex > 0,
        isRedoAvailable: () => historyIndex < history.length - 1,
        getHistory: () => history, // Добавлено: функция для получения истории
        getHistoryIndex: () => historyIndex, // Добавлено: функция для получения индекса истории
        setHistory: setHistory, // Добавлено: функция для обновления истории
        setHistoryIndex: setHistoryIndex, // Добавлено: функция для обновления индекса истории
    };
};

type EditorProps = {
    editor: EditorHandle;
    disabled?: boolean;
    mode?: string;
    onChange?: () => void;
    fileRoutes?: FileRouts
};

const uid = () => Math.random().toString(36).substr(2, 9);

const EditorComponent: React.FC<EditorProps> = ({
        editor,
        disabled = false,
        mode = "edit",
        onChange = () => {},
        fileRoutes = {
            save: {route: '', data: {}},
            delete: {route: '', data: {}}
        }
    }) => {
    const [structure, setStructure] = useState<Record<string, StructureItem>>({});
    const [value, setValue] = useState(editor.getValue());
    const [history, setHistory] = useState<Record<string, StructureItem>[]>(editor.getHistory()); // История изменений
    const [historyIndex, setHistoryIndex] = useState(editor.getHistoryIndex()); // Текущий индекс в истории

    // Синхронизация history и historyIndex с useEditor
    useEffect(() => {
        editor.setHistory(history);
    }, [history]);

    useEffect(() => {
        editor.setHistoryIndex(historyIndex);
    }, [historyIndex]);

    useEffect(() => {
        const initialStructure = buildStructure(value);
        setStructure(initialStructure);
        updateHistory(initialStructure);
    }, []);

    const updateHistory = (newStructure: Record<string, StructureItem>) => {
        const newHistory = [...history.slice(0, historyIndex + 1), newStructure]; // Добавляем новое состояние
        if (newHistory.length > 10) {
            newHistory.shift(); // Удаляем самое старое состояние, если история превышает 10 элементов
        }
        setHistory(newHistory);
        setHistoryIndex(newHistory.length - 1); // Обновляем индекс
    };

    const buildStructure = (newValue: string): Record<string, StructureItem> => {
        const defaultStructure = [{ type: "visual", data: {'text': newValue}, key: uid(), order: 1 }];
        let parsedStructure: StructureItem[];
        try {
            parsedStructure = JSON.parse(newValue);
            if (!Array.isArray(parsedStructure)) { parsedStructure = defaultStructure }
        } catch {
            parsedStructure = defaultStructure;
        }
        return parsedStructure.reduce((acc, item, index) => {
            const key = uid();
            acc[key] = { ...item, key, order: index + 1 };
            return acc;
        }, {} as Record<string, StructureItem>);
    };

    // Отмена действия
    editor.undo = () => {
        if (historyIndex > 0) {
            const prevIndex = historyIndex - 1;
            setStructure(history[prevIndex]);
            setHistoryIndex(prevIndex);
            onChange();
        }
    };

    editor.isUndoAvailable = () => {
        return historyIndex > 0;
    };

    editor.isRedoAvailable = () => {
        return historyIndex < history.length - 1;
    };

    // Повтор действия
    editor.redo = () => {
        if (historyIndex < history.length - 1) {
            const nextIndex = historyIndex + 1;
            setStructure(history[nextIndex]);
            setHistoryIndex(nextIndex);
            onChange();
        }
    };

    editor.edit = (key: string, data) => {
        if (data?.type) {//если редактируется тип
            if (structure[key].type == 'visual' && data.data && !data.data.text ) {//если был type = visual
                data.data.text = beautify(structure[key].data.text);
            }

            if (structure[key].data.text && !data.data) {
                data.data = {text: structure[key].data.text};
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
        updateHistory(updatedStructure);
        onChange();
    };

    editor.add = (afterKey, data) => {
        const newKey = uid();
        const newNode: StructureItem = {
            key: newKey,
            type: "visual",
            data: {text: ''},
            order: structure[afterKey]?.order + 1 || 0,
            ...data,
        };
        const updatedStructure = { ...structure, [newKey]: newNode };
        setStructure(updatedStructure);
        updateHistory(updatedStructure);
        onChange();
    };

    editor.delete = async (key) => {
        const item = structure[key];
        let success = false;
        if ((item.type == 'image' || item.type == 'video' ) && item.data.fileId) {
            success = false;
            const data = {file_id: item.data.fileId, ...fileRoutes.delete.data, type: item.type, path: item.data.src}
            const response = await api.safeRequestWithAlert(fileRoutes.delete.route, data);

            if (response.success || response.status == 404) {
                success = true;
            }


        }

        if (success) {
            const updatedStructure = { ...structure };
            delete updatedStructure[key];
            setStructure(updatedStructure);
            updateHistory(updatedStructure);
            onChange();
        }
    }

    editor.move = (key, step) => {
        // Получаем массив элементов, отсортированный по полю `order`
        const entries = Object.entries(structure).sort(([, a], [, b]) => a.order - b.order);

        // Находим индекс текущего элемента
        const index = entries.findIndex(([k]) => k === key);
        if (index === -1) return;

        // Вычисляем новый индекс с учетом шага `step`
        const newIndex = Math.max(0, Math.min(index + step, entries.length - 1));
        const targetKey = entries[newIndex][0];

        const updatedStructure = {
            ...structure,
            [key]: { ...structure[key], order: structure[targetKey].order },
            [targetKey]: { ...structure[targetKey], order: structure[key].order },
        };

        // Перестраиваем объект updatedStructure согласно порядку `order`
        const sortedStructure = Object.values(updatedStructure)
            .sort((a, b) => a.order - b.order)
            .reduce((acc, item) => {
                acc[item.key] = item;
                return acc;
            }, {} as Record<string, StructureItem>);

        setStructure(sortedStructure);
        updateHistory(sortedStructure);
        onChange();
    };

    editor.beautify = (key) => {
        if (structure[key]) {
            structure[key].data.text = beautify(structure[key].data.text);
            setStructure({ ...structure });
            updateHistory({ ...structure });
            onChange();
        }
    };

    editor.debug = (key: string) => {
        console.log(key, structure);
    };

    editor.getValue = () => {
        let value = JSON.stringify(
            Object.values(structure).sort((a, b) => a.order - b.order).map(({ type, data }) => ({ type, data }))
        );
        if (value == '[{"type":"visual","data":{"text":""}}]') value = '';
        return value;
    };

    editor.getBox = (key: string) => {
        return structure[key];
    };

    editor.setValue = (newValue) => {
        const oldValue = editor.getValue();
        setValue(newValue);
        const newStructure = buildStructure(newValue);
        setStructure(newStructure);
        updateHistory(newStructure); // Обновляем историю

        if (oldValue !== '[]') { // Если это не инициализационное значение
            onChange();
        }
    };

    return (
        <div className={`editor editor-${mode}-mode`}>
            {Object.entries(structure).map(([key, item]) => (
                <div key={key} className="editor-box">
                    {item.type === "visual" ? (
                        <VisualEditor
                            data={{text: item.data.text}}
                            onChange={(val: VisualEditorData) => editor.edit(key, { data: val })}
                            disabled={disabled}
                            mode={mode}
                        />
                    ) : item.type === "video" ? (
                        <VideoEditor
                            data={item.data as VideoEditorData}
                            onChange={(val: VideoEditorData) => editor.edit(key, { data: val })}
                            disabled={disabled}
                            mode={mode}
                        />
                    ) : item.type === "image" ? (
                        <ImageEditor
                            data={item.data as ImageEditorData}
                            onChange={(val: ImageEditorData) => editor.edit(key, { data: val })}
                            disabled={disabled}
                            mode={mode}
                            routes={fileRoutes}
                        />
                    ) : (
                        <CodeEditor
                            data={item.data as CodeEditorData}
                            onChange={(val: CodeEditorData) => editor.edit(key, { data: val })}
                            disabled={disabled}
                            mode={mode}
                        />
                    )}
                    <EditorToolBar boxKey={key} boxType={item.type} editor={editor} mode={mode} />
                </div>
            ))}
        </div>
    );
};

const Editor = Object.assign(EditorComponent, { useEditor });

const beautify = (code: string) => {
    try {
        return html(code, { indent_size: 2, max_preserve_newlines: 1, preserve_newlines: true });
    } catch {
        return code;
    }
};

export default Editor;
