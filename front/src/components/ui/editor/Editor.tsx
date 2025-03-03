import '@/css/ui/editor.css';
import React, { useState, useEffect } from "react";
import CodeEditor from "./CodeEditor";
import VisualEditor from "./VisualEditor/VisualEditor";
import EditorToolBar from "./EditorToolBar";
import VideoEditor from "./VideoEditor";
import ImageEditor from "./ImageEditor";
import { html } from "js-beautify";

interface StructureItem {
    key: string;
    order: number;
    type: string;
    data: string;
}

export type EditorHandle = {
    mode: string;
    setValue: (value: string) => void;
    edit: (key: string, data: Partial<StructureItem>) => void;
    add: (key: string, data: Partial<StructureItem>) => void;
    delete: (key: string) => void;
    move: (key: string, step: number) => void;
    debug: (key: string) => void;
    beautify: (key: string) => void;
    getValue: () => string;
    getBoxType: (key: string) => string;
    onChange: () => void;
};

const useEditor = (): EditorHandle => {
    const [value, setValue] = useState("");
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
        getBoxType: () => "",
        onChange: () => {},
    };
};

type EditorProps = {
    editor: EditorHandle;
    disabled?: boolean;
    mode?: string;
};

const uid = () => Math.random().toString(36).substr(2, 9);

const EditorComponent: React.FC<EditorProps> = ({ editor, disabled = false, mode = "edit" }) => {
    const [structure, setStructure] = useState<Record<string, StructureItem>>({});
    const [value, setValue] = useState(editor.getValue());

    useEffect(() => {
        setStructure(buildStructure(value));
    }, []);

    const buildStructure = (newValue: string): Record<string, StructureItem> => {
        const defaultStructure = [{ type: "visual", data: newValue, key: uid(), order: 1 }];
        let parsedStructure: StructureItem[];
        try {
            parsedStructure = JSON.parse(newValue);
            if (!parsedStructure) {parsedStructure = defaultStructure}
        } catch {
            parsedStructure = defaultStructure;
        }
        return parsedStructure.reduce((acc, item, index) => {
            const key = uid();
            acc[key] = { ...item, key, order: index + 1 };
            return acc;
        }, {} as Record<string, StructureItem>);
    };

    editor.edit = (key: string, data) => {
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

    editor.add = (afterKey, data) => {
        const newKey = uid();
        const newNode: StructureItem = {
            key: newKey,
            type: "visual",
            data: "",
            order: structure[afterKey]?.order + 1 || 0,
            ...data,
        };
        const updatedStructure = { ...structure, [newKey]: newNode };
        setStructure(updatedStructure);
        editor.onChange();
    };

    editor.delete = (key) => {
        const updatedStructure = { ...structure };
        delete updatedStructure[key];
        setStructure(updatedStructure);
        editor.onChange();
    };

    editor.move = (key, step) => {
        const entries = Object.entries(structure).sort(([, a], [, b]) => a.order - b.order);
        const index = entries.findIndex(([k]) => k === key);
        if (index === -1) return;
        const newIndex = Math.max(0, Math.min(index + step, entries.length - 1));
        const targetKey = entries[newIndex][0];

        const updatedStructure = {
            ...structure,
            [key]: { ...structure[key], order: structure[targetKey].order },
            [targetKey]: { ...structure[targetKey], order: structure[key].order },
        };
        setStructure(updatedStructure);
        editor.onChange();
    };

    editor.beautify = (key) => {
        if (structure[key]) {
            structure[key].data = beautify(structure[key].data);
            setStructure({ ...structure });
        }
    };

    editor.debug = (key: string) => {
        console.log(key, structure)
    };

    editor.getValue = () => {
        let value = JSON.stringify(
            Object.values(structure).sort((a, b) => a.order - b.order).map(({ type, data }) => ({ type, data }))
        );
        if (value == '[{"type":"visual","data":""}]') value = '';
        return value;
    };

    editor.setValue = (newValue) => {
        setValue(newValue);
        setStructure(buildStructure(newValue));
        editor.onChange();
    };

    return (
        <div className={`editor editor-${mode}-mode`}>
            {Object.entries(structure).map(([key, item]) => (
                <div key={key} className="editor-box">
                    {item.type === "visual" ? (
                        <VisualEditor
                            value={item.data}
                            onChange={(val: string) => editor.edit(key, { data: val })}
                            disabled={disabled}
                            mode = {mode}
                        />
                    ) : item.type === "video" ? (
                        <VideoEditor
                            value={item.data}
                            onChange={(val: string) => editor.edit(key, { data: val })}
                            disabled={disabled}
                            mode = {mode}
                        />
                    ) : item.type === "image" ? (
                        <ImageEditor
                            value={item.data}
                            onChange={(val: string) => editor.edit(key, { data: val })}
                            disabled={disabled}
                            mode = {mode}
                        />
                    ) : (
                        <CodeEditor value={item.data} onChange={(val) => editor.edit(key, { data: val })} type={item.type} disabled={disabled} />
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
