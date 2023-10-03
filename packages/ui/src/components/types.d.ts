
export type ButtonProps = {
    variant?: string,
    text?: boolean,
    outline?: boolean,
    small?: boolean,
    large?: boolean,
    active?: boolean,
    block?: boolean,
    href?: string,
    disabled?: boolean,
};

export type ModalProps = {
    maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl' | '3xl' | '4xl',
    visible?: boolean,
    title?: string,
    cancelTitle?: string,
    okVariant?: string,
    okOnly?: boolean,
    okTitle?: string,
    noCloseOnBackdrop?: boolean,
    isError?: boolean,
    loading?: boolean,
}
