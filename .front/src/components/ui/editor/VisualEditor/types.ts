export type VisualEditorData = {
    text: string;
}

export type VisualSourceEditorData = {
    text: string;
}

export type VisualEditorProps = {
    data: VisualEditorData;
    onChange: (value: VisualEditorData) => void;
    disabled?: boolean;
    mode: string
};

export type LinkData = {
    href: string;
    target: string;
    class: string;
};
