	document.addEventListener('DOMContentLoaded', function () {
		// 获取所有的导航链接
		const navLinks = document.querySelectorAll('.mdd-nav-link');
		
		// 获取所有的内容项
		const contentItems = document.querySelectorAll('.mdd-content-item');

		// 点击导航栏时切换显示内容
		navLinks.forEach(link => {
			link.addEventListener('click', function (e) {
				e.preventDefault();

				// 获取目标内容区域的 ID
				const targetId = this.getAttribute('data-target');
				const targetContent = document.getElementById(targetId);

				// 隐藏所有内容项
				contentItems.forEach(item => {
					item.classList.remove('active');
				});

				// 移除所有导航项的 active 类
				navLinks.forEach(link => {
					link.classList.remove('active');
				});

				// 显示当前目标内容
				targetContent.classList.add('active');

				// 为当前点击的导航链接添加 active 类
				this.classList.add('active');
			});
		});
	});
// 处理点击输入框显示下拉框
document.getElementById("departure").addEventListener("click", function() {
    console.log("点击了出发地输入框");
    document.getElementById("departure-cities").style.display = "block"; // 显示出发地下拉框
    document.getElementById("destination-cities").style.display = "none"; // 隐藏目的地下拉框
});

// 处理点击输入框显示下拉框
document.getElementById("destination").addEventListener("click", function() {
    console.log("点击了目的地输入框");
    document.getElementById("destination-cities").style.display = "block"; // 显示目的地下拉框
    document.getElementById("departure-cities").style.display = "none"; // 隐藏出发地下拉框
});

// 处理点击城市选项
function selectCity(inputId, cityName) {
    // 将选中的城市填充到对应的输入框
    document.getElementById(inputId).value = cityName;
    // 隐藏下拉框
    document.getElementById(inputId + "-cities").style.display = "none";
}

// 为城市单元格绑定点击事件
document.querySelectorAll("#departure-cities td").forEach(function(cell) {
    cell.addEventListener("click", function() {
        var cityName = cell.innerText; // 获取城市名称
        selectCity("departure", cityName); // 填充到出发地输入框
    });
});

document.querySelectorAll("#destination-cities td").forEach(function(cell) {
    cell.addEventListener("click", function() {
        var cityName = cell.innerText; // 获取城市名称
        selectCity("destination", cityName); // 填充到目的地输入框
    });
});

// 点击页面其他地方隐藏下拉框
document.addEventListener("click", function(event) {
    var departureCities = document.getElementById("departure-cities");
    var destinationCities = document.getElementById("destination-cities");

    // 如果点击的是输入框以外的地方，隐藏下拉框
    if (!event.target.closest('#departure') && !event.target.closest('#destination')) {
        departureCities.style.display = "none";
        destinationCities.style.display = "none";
    }
});

const pTags = document.querySelectorAll('.mdd-flex-biao p');

        pTags.forEach(pTag => {
            // 排除住酒店这个标签，给其他标签添加点击事件
            if (!pTag.classList.contains('no-underline')) {
                pTag.addEventListener('click', function() {
                    // 切换点击后的下划线样式
                    pTag.classList.toggle('clicked');
                });
            }
        });