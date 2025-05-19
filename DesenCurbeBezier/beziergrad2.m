function f = beziergrad2(b)

t=0:0.01:1;

B0=(1-t).^2;
B1=2*(1-t).*t;
B2=t.^2;

B=[B0;B1;B2];

f=b*B;
plot(f(1,:),f(2,:),'k');
axis([-6 6 -4.5 4.5]);
hold on;
end