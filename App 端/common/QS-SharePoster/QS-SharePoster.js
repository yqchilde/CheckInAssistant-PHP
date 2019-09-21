import _app from './app.js';
import QRCodeAlg from './QRCodeAlg.js';
const ShreUserPosterBackgroundKey = 'ShrePosterBackground_'; // 背景图片缓存名称前缀

export default function getSharePoster(obj) {
	return new Promise(async (rs, rj) => {
		try {
			_app.showLoading('正在准备海报数据')
			let {
				type,
				posterCanvasId,
				backgroundImage,
				reserve,
				qrCodeArray,
				imagesArray,
				setCanvasWH,
				setCanvasToTempFilePath,
				setDraw
			} = obj;
			let bgObj = await getShreUserPosterBackground({
				backgroundImage,
				type
			});
			if (setCanvasWH && typeof(setCanvasWH) == 'function') setCanvasWH(bgObj, type);
			if (imagesArray && imagesArray.length > 0) {
				if (typeof(imagesArray) == 'function')
					imagesArray = imagesArray(bgObj, type);
				// _app.showLoading('正在生成需绘制图片的临时路径');
				for (let i = 0; i < imagesArray.length; i++) {
					if (imagesArray[i].url) {
						let imgUrl = imagesArray[i].url
						imagesArray[i].url = await _app.downloadFile_PromiseFc(imgUrl);
						let imageInfo;
						if (imagesArray[i].infoCallBack || (imagesArray[i].circleSet && imagesArray[i].circleSet.circle))
							imageInfo = await _app.getImageInfo_PromiseFc(imgUrl);
						if (imagesArray[i].infoCallBack) {
							imagesArray[i] = { ...imagesArray[i],
								...imagesArray[i].infoCallBack(imageInfo)
							};
						}
						imagesArray[i] = { ...imagesArray[i],
							imageInfo
						}
						console.log(JSON.stringify(imagesArray[i]));
					}
				}
				_app.hideLoading();
			}
			if (qrCodeArray) {
				if (typeof(qrCodeArray) == 'function')
					qrCodeArray = qrCodeArray(bgObj, type);
				// _app.showLoading('正在生成需绘制图片的临时路径');
				for (let i = 0; i < qrCodeArray.length; i++) {
					if (qrCodeArray[i].image)
						qrCodeArray[i].image = await _app.downloadFile_PromiseFc(qrCodeArray[i].image);
				}
				// _app.hideLoading();
			}
			let poster = await drawShareImage({
				type,
				posterCanvasId,
				reserve,
				imagesArray,
				bgObj,
				qrCodeArray,
				setCanvasToTempFilePath,
				setDraw
			});
			_app.hideLoading();
			rs({
				bgObj,
				poster,
				type
			});
		} catch (e) {
			//TODO handle the exception
			rj(e);
		}
	});
}

function drawShareImage(obj) { //绘制海报方法
	let {
		type,
		posterCanvasId,
		reserve,
		bgObj,
		qrCodeArray,
		imagesArray,
		setCanvasToTempFilePath,
		setDraw
	} = obj;
	return new Promise((rs, rj) => {
		// _app.showLoading('正在绘制海报');
		let Context = uni.createCanvasContext(posterCanvasId);
		if (bgObj && bgObj.path)
			Context.drawImage(bgObj.path, 0, 0);
		if (imagesArray && imagesArray.length > 0) {
			for (let i = 0; i < imagesArray.length; i++) {
				let img = imagesArray[i];
				console.log(JSON.stringify(img));
				if (img.url) {
					if (img.circleSet && img.circleSet.circle) {
						Context.save();
						Context.beginPath();
						let circleObj = setImageCircle(img);
						Context.arc(circleObj.x, circleObj.y, circleObj.r, 0, 2 * Math.PI, false);
						Context.clip();
					}
					if (img.dWidth && img.dHeight && img.sx && img.sy && img.sWidth && img.sHeight) {
						Context.drawImage(img.url, img.dx || 0, img.dy || 0,
							img.dWidth || false, img.dHeight || false,
							img.sx || false, img.sy || false,
							img.sWidth || false, img.sHeight || false);
					} else if (img.dWidth && img.dHeight) {
						Context.drawImage(img.url, img.dx || 0, img.dy || 0,
							img.dWidth || false, img.dHeight || false);
					} else {
						Context.drawImage(img.url, img.dx || 0, img.dy || 0);
					}
					if (img.circleSet && img.circleSet.circle)
						Context.restore();
				}
			}
		}
		// _app.showToast('准备绘制自定义内容');
		if (setDraw && typeof(setDraw) == 'function') setDraw({
			Context,
			bgObj,
			type
		});
		// _app.showToast('准备绘制二维码');
		if (qrCodeArray && qrCodeArray.length > 0) {
			for (let i = 0; i < qrCodeArray.length; i++) {
				drawQrCode(Context, qrCodeArray[i]);
			}
		}
		// _app.showToast('准备执行draw方法')
		Context.draw((typeof(reserve) == 'boolean' ? reserve : false), () => {
			// _app.showToast('进入draw方法回调: ' + JSON.stringify(bgObj));
			let data = {
				x: 0,
				y: 0,
				width: bgObj.width,
				height: bgObj.height,
				// #ifdef H5
				destWidth: bgObj.width,
				destHeight: bgObj.height,
				// #endif
				// #ifndef H5
				destWidth: bgObj.width * 2,
				destHeight: bgObj.height * 2,
				// #endif
				quality: 1
			};
			if (setCanvasToTempFilePath && typeof(setCanvasToTempFilePath) == 'function')
				data = setCanvasToTempFilePath(bgObj, type);
			_app.hideLoading();
			_app.showLoading('正在输出图片');
			uni.canvasToTempFilePath({ //输出为图片
				...data,
				canvasId: posterCanvasId,
				success(res) {
					_app.hideLoading();
					rs(res);
				},
				fail(err) {
					console.log('输出图片失败:' + JSON.stringify(err));
					rj('输出图片失败:' + JSON.stringify(err))
				}
			})
		});
	});
}

function setImageCircle(obj) {
	let {
		dx,
		dy,
		dWidth,
		dHeight,
		circleSet,
		imageInfo
	} = obj;
	let {
		x,
		y,
		r
	} = circleSet;
	if (!r) {
		let d;
		if (dWidth && dHeight)
			d = dWidth > dHeight ? dHeight : dWidth;
		else
			d = imageInfo.width > imageInfo.height ? imageInfo.height : imageInfo.width;
		r = d / 2;
	}

	if (x===undefined) x = (dx || 0) + r;
	if (y===undefined) y = (dy || 0) + r;

	return {
		x,
		y,
		r
	};
}

function drawQrCode(Context, qrCodeObj) { //生成二维码方法， 参考了 诗小柒 的二维码生成器代码
	_app.showLoading('正在生成二维码');
	let qrcodeAlgObjCache = [];
	let options = {
		text: qrCodeObj.text || '', // 生成内容
		size: qrCodeObj.size || 200, // 二维码大小
		background: qrCodeObj.background || '#ffffff', // 背景色
		foreground: qrCodeObj.foreground || '#000000', // 前景色
		pdground: qrCodeObj.pdground || '#000000', // 定位角点颜色
		correctLevel: qrCodeObj.correctLevel || 3, // 容错级别
		image: qrCodeObj.image || '', // 二维码图标
		imageSize: qrCodeObj.imageSize || 40, // 二维码图标大小
		dx: qrCodeObj.dx || 0, // x轴距离
		dy: qrCodeObj.dy || 0 // y轴距离
	}
	let qrCodeAlg = null;
	let d = 0;
	for (var i = 0, l = qrcodeAlgObjCache.length; i < l; i++) {
		d = i;
		if (qrcodeAlgObjCache[i].text == options.text && qrcodeAlgObjCache[i].text.correctLevel == options.correctLevel) {
			qrCodeAlg = qrcodeAlgObjCache[i].obj;
			break;
		}
	}
	if (d == l) {
		qrCodeAlg = new QRCodeAlg(options.text, options.correctLevel);
		qrcodeAlgObjCache.push({
			text: options.text,
			correctLevel: options.correctLevel,
			obj: qrCodeAlg
		});
	}
	let getForeGround = function(config) {
		let options = config.options;
		if (options.pdground && (
				(config.row > 1 && config.row < 5 && config.col > 1 && config.col < 5) ||
				(config.row > (config.count - 6) && config.row < (config.count - 2) && config.col > 1 && config.col < 5) ||
				(config.row > 1 && config.row < 5 && config.col > (config.count - 6) && config.col < (config.count - 2))
			)) {
			return options.pdground;
		}
		return options.foreground;
	}
	let count = qrCodeAlg.getModuleCount();
	let ratioSize = options.size;
	let ratioImgSize = options.imageSize;
	//计算每个点的长宽
	let tileW = (ratioSize / count).toPrecision(4);
	let tileH = (ratioSize / count).toPrecision(4);
	//绘制
	for (let row = 0; row < count; row++) {
		for (let col = 0; col < count; col++) {
			let w = (Math.ceil((col + 1) * tileW) - Math.floor(col * tileW));
			let h = (Math.ceil((row + 1) * tileW) - Math.floor(row * tileW));
			let foreground = getForeGround({
				row: row,
				col: col,
				count: count,
				options: options
			});
			Context.setFillStyle(qrCodeAlg.modules[row][col] ? foreground : options.background);
			Context.fillRect(options.dx + Math.round(col * tileW), options.dy + Math.round(row * tileH), w, h);
		}
	}
	if (options.image) {
		let x = options.dx + Number(((ratioSize - ratioImgSize) / 2).toFixed(2));
		let y = options.dy + Number(((ratioSize - ratioImgSize) / 2).toFixed(2));
		drawRoundedRect(Context, x, y, ratioImgSize, ratioImgSize, 2, 6, true, true)
		Context.drawImage(options.image, x, y, ratioImgSize, ratioImgSize);
		// 画圆角矩形
		function drawRoundedRect(ctxi, x, y, width, height, r, lineWidth, fill, stroke) {
			ctxi.setLineWidth(lineWidth);
			ctxi.setFillStyle(options.background);
			ctxi.setStrokeStyle(options.background);
			ctxi.beginPath(); // draw top and top right corner 
			ctxi.moveTo(x + r, y);
			ctxi.arcTo(x + width, y, x + width, y + r, r); // draw right side and bottom right corner 
			ctxi.arcTo(x + width, y + height, x + width - r, y + height, r); // draw bottom and bottom left corner 
			ctxi.arcTo(x, y + height, x, y + height - r, r); // draw left and top left corner 
			ctxi.arcTo(x, y, x + r, y, r);
			ctxi.closePath();
			if (fill) {
				ctxi.fill();
			}
			if (stroke) {
				ctxi.stroke();
			}
		}
	}
	// _app.showLoading('二维码绘制完毕');
	_app.hideLoading();
}

function getShreUserPosterBackground(objs) { //检查背景图是否存在于本地， 若存在直接返回， 否则调用getShreUserPosterBackgroundFc方法
	let {
		backgroundImage,
		type
	} = objs;
	let ShreUserPosterBackgroundKeys = ShreUserPosterBackgroundKey + (type || 'default');
	return new Promise((resolve, reject) => {
		try {
			// _app.showLoading('正在获取海报背景图');
			let pbg; 
			// #ifndef H5
			pbg = _app.getStorageSync(ShreUserPosterBackgroundKeys);
			// #endif
			// #ifdef H5
			pbg = false;
			// #endif
			console.log('获取的缓存:' + JSON.stringify(pbg));
			if (pbg && pbg.path && pbg.name) {
				console.log('海报有缓存, 准备获取后端背景图进行对比');
				_app.getPosterUrl(objs).then((image) => {
					console.log('准备对比name是否相同');
                    console.log(type);
					if (pbg.name === _app.fileNameInPath(image)) {
						console.log('name相同, 判断该背景图是否存在于本地')
						_app.checkFile_PromiseFc(pbg.path).then((index) => {
							if (index >= 0) {
								console.log('海报save路径存在, 对比宽高信息, 存储并输出');
								_app.getImageInfo_PromiseFc(pbg.path).then(imageObj => {
									let obj = { ...pbg
									};
									if (!pbg.width || !pbg.height || pbg.width !== imageObj.width || pbg.height !== imageObj.height) {
										console.log('宽高对比不通过， 重新获取');
										getShreUserPosterBackgroundFc(objs, image).then(savedFilePath => {
											_app.hideLoading();
											resolve(savedFilePath)
										}).catch(err => {
											reject(err);
										});
									} else {
										console.log('宽高对比通过, 再次存储, 并返回路径');
										obj = {
											...pbg,
											width: imageObj.width,
											height: imageObj.height
										};
										_app.setStorage(ShreUserPosterBackgroundKeys, obj);
										_app.hideLoading();
										resolve(obj);
									}
								})
							} else {
								console.log('海报save路径不存在, 重新获取海报');
								getShreUserPosterBackgroundFc(objs, image).then(savedFilePath => {
									_app.hideLoading();
									resolve(savedFilePath)
								}).catch(err => {
									reject(err);
								});
							}
						});
					} else {
						console.log('name不相同, 重新获取海报');
						getShreUserPosterBackgroundFc(objs, image).then(savedFilePath => {
							_app.hideLoading();
							resolve(savedFilePath)
						}).catch(err => {
							reject(err);
						});
					}
				}).catch(err => {
					reject(err);
				});
			} else {
				console.log('海报背景图没有缓存, 准备获取海报背景图');
				getShreUserPosterBackgroundFc(objs).then(savedFilePath => {
					_app.hideLoading();
					resolve(savedFilePath)
				}).catch(err => {
					reject(err);
				});
			}
		} catch (e) {
			_app.hideLoading();
			_app.showToast('获取分享用户背景图失败:' + JSON.stringify(e));
			console.log(JSON.stringify(e));
		}
	})
}

function getShreUserPosterBackgroundFc(objs, upimage) { //下载并保存背景图方法
	let {
		backgroundImage,
		type
	} = objs;
	console.log('获取分享背景图, 尝试清空本地数据');
	let ShreUserPosterBackgroundKeys = ShreUserPosterBackgroundKey + (type || 'default');
	let pbg = _app.getStorageSync(ShreUserPosterBackgroundKeys);
	if (pbg && pbg.path && pbg.name) {
		_app.removeSavedFile(pbg.path);
		_app.removeStorageSync(ShreUserPosterBackgroundKeys);
		console.log('尝试清空本地数据结束');
	}
	return new Promise((resolve, reject) => {
		// _app.showLoading('正在下载海报背景图');
		if (upimage) {
			console.log('有从后端获取的背景图片路径');
			console.log('尝试下载并保存背景图')
			let name = _app.fileNameInPath(upimage);
			_app.downLoadAndSaveFile_PromiseFc(upimage).then(savedFilePath => {
				if (savedFilePath) {
					console.log('下载并保存背景图成功:' + savedFilePath);
					_app.getImageInfo_PromiseFc(savedFilePath).then(imageObj => {
						let returnObj = {
							path: savedFilePath,
							width: imageObj.width,
							height: imageObj.height,
							name
						}
						_app.setStorage(ShreUserPosterBackgroundKeys, returnObj);
						_app.hideLoading();
						resolve(returnObj);
					})
				} else {
					_app.hideLoading();
					reject('not find savedFilePath');
				}
			});
		} else {
			console.log('没有从后端获取的背景图片路径, 尝试从后端获取背景图片路径');
			_app.getPosterUrl(objs).then(image => {
				console.log('尝试下载并保存背景图')
				let name = _app.fileNameInPath(image);
				_app.downLoadAndSaveFile_PromiseFc(image).then(savedFilePath => {
					if (savedFilePath) {
						console.log('下载并保存背景图成功:' + savedFilePath);
						_app.getImageInfo_PromiseFc(savedFilePath).then(imageObj => {
							console.log('获取图片信息成功');
							let returnObj = {
								path: savedFilePath,
								width: imageObj.width,
								height: imageObj.height,
								name
							}
							
							// #ifndef H5
							_app.setStorage(ShreUserPosterBackgroundKeys, returnObj);
							// #endif
							
							_app.hideLoading();
							resolve(returnObj);
						}).catch(err=>{
							_app.hideLoading();
							console.log('获取图片信息失败:' + JSON.stringify(err));
						})
					} else {
						_app.hideLoading();
						reject('not find savedFilePath');
					}
				});
			}).catch(err => {
				reject(err);
			});
		}

	});
}
