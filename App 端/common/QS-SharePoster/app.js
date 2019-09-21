let _app = {
	getPosterUrl(objs) { // 后端获取背景图的url路径方法
		let {
			backgroundImage,
			type
		} = objs;
		return new Promise((rs, rj) => {//resolve 和 reject
			let image;
			if (backgroundImage)
				image = backgroundImage;
			else
				switch (type) { //根据type获取背景图, 一般要改成request获取
					case 1:
						image = '';
						break;
					default:
						// image = 'https://ae01.alicdn.com/kf/HTB1iTw3a8r0gK0jSZFn762RRXXaM.png';
						image = 'https://ae01.alicdn.com/kf/He351bce0c57141fe8095a4a4f7f8050a4.png';
						break;
				}
			if(image)
				rs(image); // resolve图片的路径
			else
				rj('背景图片路径不存在');
		})
	},






	//下面一般不用动他
	shareTypeListSheetArray: {
		array: [0, 1, 2, 3, 4, 5]
	}, // 分享类型 0-图文链接 1-纯文字 2-纯图片 3-音乐 4-视频 5-小程序
	getStorage(key, scb, fcb) {
		uni.getStorage({
			key,
			success: function(res) {
				if (res.data && res.data != "") {
					if (scb) scb(res.data);
				} else {
					if (fcb) fcb();
				}
			},
			fail: function() {
				if (fcb) fcb();
			}
		})
	},
	setStorage(key, data) {
		console.log('设置缓存')
		console.log('key：' + key)
		console.log('data：' + JSON.stringify(data));
		uni.setStorage({
			key,
			data
		})
	},
	setStorageSync(key, data) {
		uni.setStorageSync(key, data);
	},
	getStorageSync(key) {
		return uni.getStorageSync(key);
	},
	clearStorageSync() {
		uni.clearStorageSync();
	},
	removeStorageSync(key) {
		uni.removeStorageSync(key);
	},
	showLoading(msg, ifmask) {
		uni.showLoading({
			title: msg,
			mask: ifmask || false
		})
	},
	hideLoading() {
		uni.hideLoading();
	},
	showToast(msg, icon) {
		uni.showToast({
			title: msg,
			icon: icon || 'none'
		})
	},
	getImageInfo(url, cb, fcb) {
		uni.getImageInfo({
			src: url,
			success(res) {
				if (cb && typeof(cb) == 'function') cb(res);
			},
			fail(err) {
				if (fcb && typeof(fcb) == 'function') fcb(err);
			}
		})
	},
	downloadFile(url, cb) {
		uni.downloadFile({
			url,
			success(res) {
				if (cb && typeof(cb) == 'function') cb(res);
			}
		})
	},
	downloadFile_PromiseFc(url) {
		return new Promise((rs, rj) => {
			if (url.substring(0, 4) !== 'http')
				rs(url);
			else
				uni.downloadFile({
					url,
					success(res) {
						if (res && res.tempFilePath)
							rs(res.tempFilePath);
						else
							rj('not find tempFilePath');
					},
					fail(err) {
						rj(err);
					}
				})
		});
	},
	saveFile(url) {
		uni.saveFile({
			tempFilePath: url,
			success(res) {
				console.log('保存成功:' + JSON.stringify(res))
			}
		})
	},
	downLoadAndSaveFile_PromiseFc(url) {
		return new Promise((rs, rj) => {
			if (url.substring(0, 4) === 'http')
				uni.downloadFile({
					url,
					success(res) {
						if (res && res.tempFilePath) {
							
							// #ifdef H5
							rs(res.tempFilePath);
							// #endif
							
							// #ifndef H5
							uni.saveFile({
								tempFilePath: res.tempFilePath,
								success(res) {
									if (res && res.savedFilePath)
										rs(res.savedFilePath);
									else
										rj('not find savedFilePath');
								},
								fail(err) {
									rj(err);
								}
							})
							// #endif
							
						} else {
							rj('not find tempFilePath');
						}
					},
					fail(err) {
						rj(err);
					}
				})
			else
				rs(url);
		})
	},
	checkFile_PromiseFc(url) {
		return new Promise((rs, rj) => {
			uni.getSavedFileList({
				success(res) {
					let d = res.fileList;
					let index = d.findIndex(item => {
						return item.filePath === url;
					})
					rs(index);
				},
				fail(err) {
					rj(err);
				}
			})
		});
	},
	removeSavedFile(path) {
		uni.getSavedFileList({
			success(res) {
				let d = res.fileList;
				let index = d.findIndex(item => {
					return item.filePath === path;
				});
				if (index >= 0)
					uni.removeSavedFile({
						filePath: path
					})
			}
		})
	},
	fileNameInPath(path) {
		let s = path.split("/");
		return s[s.length - 1];
	},
	getImageInfo_PromiseFc(imgPath) {
		return new Promise((rs, rj) => {
			uni.getImageInfo({
				src: imgPath,
				success: res => {
					rs(res);
				},
				fail: err => {
					rj(err)
				}
			})
		});
	},
	previewImage(urls) {
		if (typeof(urls) == 'string')
			urls = [urls];
		uni.previewImage({
			urls,
		})
	},
	actionSheet(obj, cb) {
		let sheetArray = [];
		for (let i = 0; i < obj.array.length; i++) {
			switch (obj.array[i]) {
				case 'sinaweibo':
					sheetArray[i] = '新浪微博';
					break;
				case 'qq':
					sheetArray[i] = 'QQ';
					break;
				case 'weixin':
					sheetArray[i] = '微信';
					break;
				case 'WXSceneSession':
					sheetArray[i] = '微信好友';
					break;
				case 'WXSenceTimeline':
					sheetArray[i] = '微信朋友圈';
					break;
				case 'WXSceneFavorite':
					sheetArray[i] = '微信收藏';
					break;
				case 0:
					sheetArray[i] = '图文链接';
					break;
				case 1:
					sheetArray[i] = '纯文字';
					break;
				case 2:
					sheetArray[i] = '纯图片';
					break;
				case 3:
					sheetArray[i] = '音乐';
					break;
				case 4:
					sheetArray[i] = '视频';
					break;
				case 5:
					sheetArray[i] = '小程序';
					break;
				default:
					break;
			}
		}
		this.showActionSheet(sheetArray, cb);
	},
	showActionSheet(sheetArray, cb) {
		uni.showActionSheet({
			itemList: sheetArray,
			success: (e) => {
				if (cb && typeof(cb) == 'function') cb(e.tapIndex);
			}
		})
	},
	getProvider(type, cb, sheet) {
		let _this = this;
		uni.getProvider({
			service: type,
			success: function(res) {
				if (sheet) {
					let obj = {};
					obj.array = res.provider;
					_this.actionSheet(obj, function(index) {
						if (cb && typeof(cb) == "function") cb(res.provider[index]);
					});
				} else {
					if (type == 'payment') {
						let providers = res.provider;
						let payTypeArray = [];
						for (let i = 0; i < providers.length; i++) {
							if (providers[i] == 'wxpay') {
								payTypeArray[i] = {
									name: '微信支付',
									value: providers[i],
									img: '/static/image/wei.png'
								};
							} else if (providers[i] == 'alipay') {
								payTypeArray[i] = {
									name: "支付宝支付",
									value: providers[i],
									img: '/static/image/ali.png'
								};
							}
						}
						if (cb && typeof(cb) == "function") cb(payTypeArray);
					} else {
						if (cb && typeof(cb) == "function") cb(res);
					}
				}
			},
		})
	},
	// #ifdef APP-PLUS
	getShare(providerName, WXScene, shareType, title, summary, href, imageUrl, miniProgramObj, mediaUrl, scb, fcb) { //miniProgram: {path: '', type: 0, webUrl: ''}
		let _this = this;
		if (typeof(shareType) == 'number' && !isNaN(shareType) && shareType >= 0) {
			_this.readyShare(providerName, WXScene, shareType, title, summary, href, imageUrl, miniProgramObj, mediaUrl, scb,
				fcb);
		} else {
			_this.actionSheet(_this.shareTypeListSheetArray, function(index) {
				_this.readyShare(providerName, WXScene, _this.shareTypeListSheetArray.array[index], title, summary, href,
					imageUrl, miniProgramObj, mediaUrl, scb, fcb);
			});
		}
	},
	readyShare(providerName, WXScene, shareType, title, summary, href, imageUrl, miniProgramObj, mediaUrl, scb, fcb) {
		let _this = this;
		let shareObjData = {};
		switch (shareType) {
			case 0:
				shareObjData = {
					href: href,
					summary: summary,
					imageUrl: imageUrl
				};
				break;
			case 1:
				shareObjData = {
					summary: summary,
					href: href
				};
				break;
			case 2:
				shareObjData = {
					imageUrl: imageUrl
				};
				break;
			case 3:
				if (mediaUrl) {
					_this.showToast('暂不支持此分享类型');
					return;
				};
				shareObjData = {
					mediaUrl: mediaUrl
				};
				break;
			case 4:
				if (mediaUrl) {
					_this.showToast('暂不支持此分享类型');
					return;
				};
				shareObjData = {
					mediaUrl: mediaUrl
				};
				break;
			case 5:
				shareObjData = {
					miniProgram: { ...miniProgramObj,
						id: miniProgramId,
						type: miniProgramShareType
					},
					imageUrl: imageUrl
				};
				providerName = 'weixin';
				break;
			default:
				_this.showToast('分享参数-shareType错误');
				return;
				break;
		}
		shareObjData.title = title;
		_this.share(providerName, WXScene, shareType, shareObjData, function(res) {
			if (scb && typeof(scb) == 'function') scb(res);
		}, function(err) {
			if (fcb && typeof(fcb) == 'function') fcb(err);
		});
	},
	share(providerName, WXScene, shareType, data, scb, fcb) {
		let _this = this;
		let shareObj = {
			provider: '',
			success: Function,
			fail: Function
		};
		if (providerName && providerName != '') {
			shareObj.provider = providerName;
			if (providerName == 'weixin') {
				_this.readyShareWXScene(WXScene, function(Scene) {
					shareObj.scene = Scene;
					_this.doingShare(shareObj, shareType, data, scb, fcb);
				});
			} else {
				_this.doingShare(shareObj, shareType, data, scb, fcb);
			}
		} else {
			_this.getProvider('share', function(name) {
				shareObj.provider = name;
				if (name == 'weixin') {
					_this.readyShareWXScene(WXScene, function(Scene) {
						shareObj.scene = Scene;
						_this.doingShare(shareObj, shareType, data, scb, fcb);
					});
				} else {
					_this.doingShare(shareObj, shareType, data, scb, fcb);
				}
			}, true);
		}
	},
	readyShareWXScene(WXScene, cb) {
		let _this = this;
		let WXScenetypelist = {
			array: ['WXSceneSession', 'WXSenceTimeline', 'WXSceneFavorite']
		};
		if (WXScene && WXScene != "") {
			if (cb && typeof(cb) == 'function') cb(WXScene);
		} else {
			_this.actionSheet(WXScenetypelist, function(index) {
				if (cb && typeof(cb) == 'function') cb(WXScenetypelist.array[index]);
			});
		}
	},
	doingShare(shareObj, shareType, data, scb, fcb) {
		shareObj.type = shareType;
		if (data && data.title) shareObj.title = data.title;
		switch (shareType) {
			case 0: //图文链接
				shareObj.href = data.href;
				shareObj.summary = data.summary;
				shareObj.imageUrl = data.imageUrl;
				break;
			case 1: //纯文字
				shareObj.summary = data.summary;
				shareObj.href = data.href;
				break;
			case 2: //纯图片
				shareObj.imageUrl = data.imageUrl;
				break;
			case 3: //音乐
				if (!data.mediaUrl) {
					_this.showToast('暂不支持此分享类型');
					return;
				};
				shareObj.mediaUrl = data.mediaUrl;
				break;
			case 4: //视频
				if (!data.mediaUrl) {
					_this.showToast('暂不支持此分享类型');
					return;
				};
				shareObj.mediaUrl = data.mediaUrl;
				break;
			case 5: //小程序
				if (miniProgramId == '') {
					uni.showToast('未设置小程序ID, 请使用其他方式分享');
					return;
				}
				let mp = {
					id: miniProgramId
				};
				mp.path = data.miniProgram.path;
				mp.type = data.miniProgram.type;
				if (data.miniProgram.webUrl) mp.webUrl = data.miniProgram.webUrl;
				shareObj.miniProgram = mp;
				shareObj.imageUrl = data.imageUrl;
				break;
			default:
				_app.showToast('分享参数-shareType错误');
				break;
		}
		shareObj.success = function(res) {
			if (scb && typeof(scb) == 'function') scb(res);
		}
		shareObj.fail = function(err) {
			if (fcb && typeof(fcb) == 'function') fcb(err);
		}
		console.log(JSON.stringify(shareObj));
		uni.share(shareObj);
	},
	// #endif
}
export default _app;
