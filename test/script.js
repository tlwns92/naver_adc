window.onload = function() {
    var canvas = document.getElementById("graphCanvas");
    var context = canvas.getContext("2d");

    // 그래프 크기 및 여백 설정
    var graphWidth = canvas.width - 40;
    var graphHeight = canvas.height - 40;
    var marginLeft = 40;
    var marginBottom = 20;

    // 좌표값 배열
    var points = [
        { x: 50, y: 100 },
        { x: 150, y: 200 },
        { x: 250, y: 50 },
        // 추가 좌표값들...
    ];

    // 그래프 그리기
    context.fillStyle = "blue";
    context.strokeStyle = "black";
    context.font = "bold 16px Arial";
    context.fillStyle = "black";

    // x 축 그리기
    context.beginPath();
    context.moveTo(marginLeft, canvas.height - marginBottom);
    context.lineTo(marginLeft + graphWidth, canvas.height - marginBottom);
    context.lineTo(marginLeft + graphWidth - 10, canvas.height - marginBottom - 5);
    context.moveTo(marginLeft + graphWidth, canvas.height - marginBottom);
    context.lineTo(marginLeft + graphWidth - 10, canvas.height - marginBottom + 5);
    context.stroke();

    // x 축 표시
    context.textAlign = "end";
    context.fillText("x축", marginLeft + graphWidth - 10, canvas.height - marginBottom + 20);

    // y 축 그리기
    context.beginPath();
    context.moveTo(marginLeft, canvas.height - marginBottom);
    context.lineTo(marginLeft, marginBottom);
    context.lineTo(marginLeft - 5, marginBottom + 10);
    context.moveTo(marginLeft, marginBottom);
    context.lineTo(marginLeft + 5, marginBottom + 10);
    context.stroke();

    // y 축 표시
    context.textAlign = "end";
    context.fillText("y축", marginLeft - 10, marginBottom + 5);

    // 좌표값에 따라 점 그리기
    for (var i = 0; i < points.length; i++) {
        var point = points[i];
        var x = marginLeft + point.x;
        var y = canvas.height - marginBottom - point.y;

        // 좌표 점 그리기
        context.beginPath();
        context.arc(x, y, 5, 0, 2 * Math.PI);
        context.fillStyle = "darkblue";
        context.fill();
        context.strokeStyle = "black";
        context.stroke();
    }
};
