const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('employees', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    fname: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    middlename: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    lname: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    username: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    slab_type: {
      type: DataTypes.STRING(250),
      allowNull: false
    },
    type: {
      type: DataTypes.STRING(20),
      allowNull: false,
      defaultValue: "0"
    },
    dob: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    gender: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    mobile: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    created: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false,
      defaultValue: "Y"
    },
    department_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    joiningdate: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    email: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    experience: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    martial_status: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    nationality: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    designation_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    file: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    f_h_name: {
      type: DataTypes.STRING(150),
      allowNull: false
    },
    emp_status: {
      type: DataTypes.STRING(95),
      allowNull: false
    },
    p_department: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    p_designation: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    CL: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    PL: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    half_pay: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    maternity: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    CL_avail: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    PL_avail: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    half_pay_avail: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    mat_avail: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    is_drop: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: true,
      defaultValue: "N"
    },
    drop_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    token: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    notification_counter: {
      type: DataTypes.INTEGER,
      allowNull: true,
      defaultValue: 0
    },
    otp: {
      type: DataTypes.INTEGER,
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'employees',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
      {
        name: "id",
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
