import React, { useState, useEffect } from "react";

type EditorHandle = {
    setValue: (value: string) => void;
    getValue: () => string;
    onChange: (value: string) => void;
};

const useEditor = (): EditorHandle => {
    return {
        setValue: (_value: string) => {},
        getValue: () => "",
        onChange: (_value: string) => {}
    };
};

type EditorProps = {
    editor: EditorHandle;
};
//const Editor: React.FC<EditorProps> & { useEditor: () => EditorHandle } = ({ editor }) => {
//const Editor: ({editor}: { editor: any }) => React.JSX.Element = ({ editor }) => {
const EditorComponent: React.FC<EditorProps> = ({ editor }) => {
    const [value, setValue] = useState("");

    useEffect(() => {
        editor.onChange(value);
        editor.setValue = (val: string) => setValue(val);
        editor.getValue = () => value;
    }, [editor, value]);

    return <textarea value={value} onChange={(e) => setValue(e.target.value)} style={{ width: "100%", height: "150px" }} />;
};

// Приведение типа, чтобы PhpStorm не ругался
const Editor = Object.assign(EditorComponent, { useEditor });

Editor.useEditor = useEditor;

export default Editor;
