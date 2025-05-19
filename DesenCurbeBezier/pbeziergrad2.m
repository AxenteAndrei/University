function f = pbeziergrad2(b)
t=0:0.01:1;

B0=(1-t).^2;
B1=2*(1-t).*t;
B2=t.^2;

B=[B0;B1;B2];

f=b*B;

plot(b(1,:),b(2,:),'b-');
plot(f(1,:),f(2,:),'k', 'LineWidth', 3);
axis([-6 6 -4.5 4.5]);
hold on;

scatter([b(1,1), b(1,end)], [b(2,1), b(2,end)], 100, 'k', 'filled')
scatter(b(1,:), b(2,:), 100, 'k');
end