function search() {
    var query = document.getElementById('searchInput').value;  // 获取搜索框的内容
    if (query) {
        var url = 'https://www.baidu.com/s?wd=' + encodeURIComponent(query);  // 构建搜索结果的 URL
        window.location.href = url;  // 跳转到指定网站
    } else {
        alert("请输入搜索内容");  // 如果搜索框为空，提示用户输入内容
    }
}
