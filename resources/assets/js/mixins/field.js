export const FieldValue = {
    props: {
        value: {
            type: [String, Number, Boolean, Object, Array]
        },
        updateData: Function
    }
};

export const UpdateData = {
    props: {
        updateData: Function
    }
};