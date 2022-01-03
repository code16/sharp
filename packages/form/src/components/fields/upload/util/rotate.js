
export function rotate(cropper, degree) {
    const data = cropper.getData();

    rotateTo(cropper, data.rotate + degree);
}

export function rotateTo(cropper, degree) {
    const data = cropper.getCropBoxData();
    const contData = cropper.getContainerData();

    //set data of cropbox to avoid unwanted behavior due to strict mode
    data.width = 2;
    data.height = 2;
    data.top = 0;
    let leftNew = (contData.width / 2) - 1;
    data.left = leftNew;
    cropper.setCropBoxData(data);
    //rotate
    cropper.rotateTo(degree);
    //get canvas data
    let canvData = cropper.getCanvasData();
    //calculate new height and width based on the container dimensions
    let heightOld = canvData.height;
    let heightNew = contData.height;
    let koef = heightNew / heightOld;
    let widthNew = canvData.width * koef;
    canvData.height = heightNew;
    canvData.width = widthNew;
    canvData.top = 0;
    if (canvData.width >= contData.width) {
        canvData.left = 0;
    }
    else {
        canvData.left = (contData.width - canvData.width) / 2;
    }
    cropper.setCanvasData(canvData);
    //and now set cropper "back" to full crop
    data.left = 0;
    data.top = 0;
    data.width = canvData.width;
    data.height = canvData.height;
    cropper.setCropBoxData(data);
}
