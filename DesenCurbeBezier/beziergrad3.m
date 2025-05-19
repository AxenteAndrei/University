function f = beziergrad3(b)

t=0:0.01:1;

B0=(1-t).^3;
B1=3*(1-t).^2.*t;
B2=3*(1-t).*t.^2;
B3=t.^3;

B=[B0;B1;B2;B3];

f=b*B;
plot(f(1,:),f(2,:),'k');
axis([-6 6 -4.5 4.5]);
hold on;
end