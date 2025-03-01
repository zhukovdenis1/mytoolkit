export type VisualEditorProps = {
    value: string;
    onChange: (value: string) => void;
    disabled?: boolean;
    mode: string
};

export type LinkData = {
    href: string;
    target: string;
    class: string;
};
