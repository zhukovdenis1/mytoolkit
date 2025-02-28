import { Spin as MySpin, SpinProps } from "antd";

const Spin = (props: SpinProps) => <MySpin size="large" {...props} />;

export {
    Button,
    Form,
    Input,
    message,
    Select,
    Space,
    Table,
    Tree,
    TreeSelect,
} from "antd";

export { Spin };
export { default as Editor } from "@/components/ui/editor/Editor";
export { showModal } from "@/components/ui/Modal";
export { Confirmable } from "./Confirmable";
