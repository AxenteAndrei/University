function f = beziergrad4(b)
t=0:0.01:1;

B0=(1-t).^4;
B1=4*(1-t).^3.*t;
B2=6*(1-t).^2.*t.^2;
B3=4*(1-t).*t.^3;
B4=t.^4;

B=[B0;B1;B2;B3;B4];

f=b*B;
plot(f(1,:),f(2,:),'k');
axis([-6 6 -4.5 4.5]);
hold on;
end